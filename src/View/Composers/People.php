<?php

namespace People\View\Composers;
use Roots\Acorn\View\Composer;
use WP_Query;

class People extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.builder.people',
    ];

    public function with(): array
    {
        return [
            'teams' => $this->getGroups(),
            'peoples' => $this->getPeoples(),
        ];
    }

    public function getGroups(): array
    {
        $acf_groups = $this->getPartialData('groups');

        if (is_array($acf_groups) && !empty($acf_groups)) {
            usort($acf_groups, function($a, $b) {
                $posA = get_field('tax_position', 'people_group_' . $a->term_id);
                $posB = get_field('tax_position', 'people_group_' . $b->term_id);

                return $posA <=> $posB;
            });

            return $acf_groups;
        }

        return get_terms([
            'taxonomy' => 'people_group',
            'hide_empty' => false,
            'meta_key' => 'tax_position',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        ]);
    }


    private function getPeoples(): array
    {
        global $post;

        $args = [
            'post_type'      => 'people',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $peoples = [];
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $unsorted = [];

            while ($query->have_posts()) {
                $query->the_post();

                $person_teams = wp_get_post_terms($post->ID, 'people_group');

                foreach ($person_teams as $team) {
                    $unsorted[] = [
                        'ID'           => get_the_ID(),
                        'slug'         => $post->post_name . '-' . $team->slug,
                        'title'        => get_the_title(),
                        'position'     => get_field('position'),
                        'descriptions' => get_field('descriptions'),
                        'photo'        => get_field('photo'),
                        'teams'        => $team->slug,
                        'team_id'      => $team->term_id,
                    ];
                }
            }

            wp_reset_postdata();

            // Sort by group-specific people_post_order
            $sorted = [];
            $groups = get_terms([
                'taxonomy' => 'people_group',
                'hide_empty' => false,
            ]);

            foreach ($groups as $group) {
                $order = get_term_meta($group->term_id, 'people_post_order', true);
                if (!is_array($order)) $order = [];

                foreach ($order as $person_id) {
                    foreach ($unsorted as $index => $person) {
                        // Match person ID AND the group
                        if ($person['ID'] === $person_id && $person['team_id'] === $group->term_id) {
                            $sorted[] = $person;
                            unset($unsorted[$index]); // remove to avoid duplicates
                        }
                    }
                }
            }

            // Add any remaining people (not ordered)
            foreach ($unsorted as $person) {
                $sorted[] = $person;
            }

            $peoples = $sorted;

            return $peoples;
        }
    }


    /**
     * Allows you to get variables that would already be present in the partial
     * @todo-wp_template Migrate this method to a parent class
     * @param $key
     * @return mixed
     */
    public function getPartialData($key)
    {
        return $this->view->getData()[$key];
    }
}
