<?php

namespace People\Providers;
use Copia\CustomPostTypes as CPT;

class RegisterPostType implements Provider
{
    public const TAXONOMY = 'people_group';

    public function __construct()
    {
        add_action('init', [$this, 'cpt_register']);

        // Groups are one level deep. The taxonomy is only hierarchical so the
        // editor renders checkboxes, so any parent that gets through the admin
        // screens, the REST API or WP-CLI is flattened back to the top level.
        add_filter('wp_update_term_parent', [$this, 'forceFlatParent'], 10, 3);
        add_action('created_' . self::TAXONOMY, [$this, 'resetParent']);
    }

    /**
     * @param int    $parent
     * @param int    $term_id
     * @param string $taxonomy
     * @return int
     */
    public function forceFlatParent($parent, $term_id, $taxonomy)
    {
        return $taxonomy === self::TAXONOMY ? 0 : $parent;
    }

    /**
     * wp_insert_term() writes the parent straight to the database with no
     * filter of its own, so a newly created group is corrected afterwards.
     */
    public function resetParent($term_id): void
    {
        $term = get_term($term_id, self::TAXONOMY);

        if ($term instanceof \WP_Term && (int) $term->parent !== 0) {
            wp_update_term($term_id, self::TAXONOMY, ['parent' => 0]);
        }
    }

    public function register()
    {
        //
    }

    public function cpt_register() {
        $types = [];

        $isPubliclyQueryable = PeopleSettings::isViewPageEnabled();

        array_push($types, CPT::createPostType('people', 'People', 'People')
            ->setPublic(true)
            ->setPubliclyQueryable($isPubliclyQueryable)
            ->setShowInRest(true)
            ->setMenuPosition(25)
            ->setMenuIcon('dashicons-groups')
            // 'custom-fields' is what makes the REST controller expose the
            // `meta` field the Person Details panel reads and writes. It does
            // not surface the classic custom-fields metabox.
            ->setSupports(['title', 'editor', 'thumbnail', 'revisions', 'custom-fields'])
            ->setRewrite([
                'slug' => 'person',
                'with_front' => false
            ]),
        );

        array_push($types, CPT::createTaxonomy('people_group', 'people', 'Group')
            ->setPubliclyQueryable(false)
            // Without this the block editor renders no Group panel at all —
            // the taxonomy would only be reachable from the Groups admin screen.
            ->setShowInRest(true)
            // Hierarchical purely for the UI: groups are a curated set, so the
            // editor should offer a checkbox list rather than a free-text tag
            // box that creates a new group on every typo. Groups are still a
            // flat, single-level list — see forceFlatParent() below.
            ->setHierarchical(true)
        );

        $types = apply_filters('people_tax_before_insert', $types);

        CPT::register($types, false);
    }
}
