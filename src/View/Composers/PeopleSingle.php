<?php

namespace People\View\Composers;

use Roots\Acorn\View\Composer;
use People\Providers\PeopleSettings;
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
        global $post;
        $args = [
            'title' => get_the_title(),
            'photo' => get_field('photo'),
            'position' => get_field('position'),
            'content' => get_field('descriptions'),
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

        $enableManualRelated = get_field('choose_manual_related_team');
        $getManualRelated = get_field('manual_related_team');
        $relatedPosts = [];

        if($enableManualRelated && $getManualRelated) {
            foreach($getManualRelated as $item) {
                $fields['title'] = get_the_title($item->ID);
                $fields['photo'] = get_field('photo', $item->ID);
                $fields['position'] = get_field('position', $item->ID);
                $fields['permalink'] = get_permalink($item->ID);

                $relatedPosts[] = $fields;
            }
        } else {
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
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $fields['title'] = get_the_title($post->ID);
                    $fields['photo'] = get_field('photo', $post->ID);
                    $fields['position'] = get_field('position', $post->ID);
                    $fields['link'] = get_permalink($post->ID);

                    $relatedPosts[] = $fields;
                }
                wp_reset_postdata();
            }
        }


        return $relatedPosts;
    }
}
