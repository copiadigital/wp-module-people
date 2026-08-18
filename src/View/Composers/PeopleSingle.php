<?php

namespace People\View\Composers;

use Roots\Acorn\View\Composer;
use People\Providers\PeopleSettings;
use People\Support\PersonData;
use WP_Query;

class PeopleSingle extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.content-single-people'
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        $args = [
            'title' => get_the_title(),
            'photo' => PersonData::photo(),
            'position' => PersonData::position(),
            'content' => PersonData::bio(),
            'relatedPeoples' => $this->getRelatedPeoplesByGroup(),
            'showRelatedMembers' => PeopleSettings::showRelatedMembers(),
            'relatedMembersTitle' => PeopleSettings::getRelatedMembersTitle(),
        ];

        return $args;
    }

    /**
     * @return array
     */
    private function getRelatedPeoplesByGroup()
    {
        global $post;

        if (PersonData::usesManualRelated()) {
            return array_map(
                fn($id) => PersonData::summary($id),
                PersonData::manualRelatedIds()
            );
        }

        $terms = wp_get_post_terms($post->ID, 'people_group');
        $term_ids = !empty($terms) && !is_wp_error($terms) ? wp_list_pluck($terms, 'term_id') : [];

        $args = array(
            'post_type' => 'people',
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'post__not_in' => [$post->ID],
            'orderby' => 'rand',
            'paged' => get_query_var('paged') ?: 1,
        );

        if (!empty($term_ids)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'people_group',
                    'field' => 'term_id',
                    'terms' => $term_ids,
                ]
            ];
        }

        $query = new WP_Query($args);

        $relatedPosts = array_map(
            fn($id) => PersonData::summary($id),
            wp_list_pluck($query->posts, 'ID')
        );

        wp_reset_postdata();

        return $relatedPosts;
    }
}
