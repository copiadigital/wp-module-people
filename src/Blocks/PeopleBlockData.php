<?php

namespace People\Blocks;

use People\Providers\RegisterMeta;
use People\Providers\RegisterPostType;
use People\Support\PersonData;
use WP_Query;
use WP_Term;

/**
 * Query layer for the theme/people block.
 *
 * Mirrors what People\View\Composers\People does for the ACF page-builder
 * layout, reading through PersonData so both stay on the same storage.
 */
class PeopleBlockData
{
    /**
     * Groups to show, in the curated `tax_position` order.
     *
     * @return WP_Term[]
     */
    public static function groups(array $attributes): array
    {
        $chosen = array_filter(array_map('intval', (array) ($attributes['groups'] ?? [])));

        $terms = get_terms([
            'taxonomy' => RegisterPostType::TAXONOMY,
            'hide_empty' => false,
            'include' => $chosen ?: null,
        ]);

        if (is_wp_error($terms) || ! is_array($terms)) {
            return [];
        }

        usort($terms, function (WP_Term $a, WP_Term $b) {
            $posA = (int) get_term_meta($a->term_id, 'tax_position', true);
            $posB = (int) get_term_meta($b->term_id, 'tax_position', true);

            return $posA <=> $posB;
        });

        return $terms;
    }

    /**
     * Rows to render. Grid and tabs emit one row per person per group so a
     * person appearing in two groups shows under both; the slider is a flat
     * list.
     */
    public static function people(array $attributes): array
    {
        $style = $attributes['style'] ?? 'tabs';

        if ($style === 'slider') {
            return self::sliderPeople($attributes);
        }

        $groups = self::groups($attributes);

        if (! $groups) {
            return [];
        }

        $rows = [];

        foreach ($groups as $group) {
            foreach (self::peopleInGroup($group) as $person) {
                $rows[] = PersonData::summary($person) + ['group_id' => (int) $group->term_id];
            }
        }

        return $rows;
    }

    /**
     * Post IDs in a group, honouring the group's curated `people_post_order`
     * and appending anything that ordering does not mention.
     *
     * @return int[]
     */
    protected static function peopleInGroup(WP_Term $group): array
    {
        $query = new WP_Query([
            'post_type' => RegisterMeta::POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'tax_query' => [[
                'taxonomy' => RegisterPostType::TAXONOMY,
                'field' => 'term_id',
                'terms' => $group->term_id,
            ]],
        ]);

        $ids = array_map('intval', $query->posts);

        $order = get_term_meta($group->term_id, 'people_post_order', true);
        $order = is_array($order) ? array_map('intval', $order) : [];

        $ordered = array_values(array_intersect($order, $ids));

        return array_merge($ordered, array_values(array_diff($ids, $ordered)));
    }

    protected static function sliderPeople(array $attributes): array
    {
        if (($attributes['showPeopleBasedOn'] ?? 'default') === 'manual_posts') {
            $ids = array_filter(array_map('intval', (array) ($attributes['manualPosts'] ?? [])));

            return array_map(fn($id) => PersonData::summary($id), $ids);
        }

        $query = new WP_Query([
            'post_type' => RegisterMeta::POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);

        return array_map(fn($id) => PersonData::summary($id), array_map('intval', $query->posts));
    }
}
