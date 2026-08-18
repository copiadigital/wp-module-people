<?php

namespace People\View\Composers;
use Roots\Acorn\View\Composer;
use People\Support\PersonData;
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
            'style' => $this->getPartialData('style'),
            'type' => $this->getPartialData('style') === 'slider'
                ? $this->getPartialData('slider_type')
                : $this->getPartialData('type'),
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
        if ($this->getPartialData('style') === 'slider') {
            return $this->getSliderPeoples();
        }

        $query = new WP_Query([
            'post_type'      => 'people',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        if (!$query->have_posts()) {
            return [];
        }

        // One row per person *per group* — the tabs/grid layouts render the
        // same person under each group they belong to.
        $unsorted = [];

        foreach ($query->posts as $person) {
            foreach (wp_get_post_terms($person->ID, 'people_group') as $team) {
                $unsorted[] = PersonData::summary($person->ID) + [
                    'slug'    => $person->post_name . '-' . $team->slug,
                    'teams'   => $team->slug,
                    'team_id' => $team->term_id,
                ];
            }
        }

        return $this->sortByGroupOrder($unsorted);
    }

    /**
     * Apply each group's manually curated `people_post_order`, then append
     * anything that group's ordering doesn't mention.
     */
    private function sortByGroupOrder(array $unsorted): array
    {
        $sorted = [];

        $groups = get_terms([
            'taxonomy'   => 'people_group',
            'hide_empty' => false,
        ]);

        foreach ($groups as $group) {
            $order = get_term_meta($group->term_id, 'people_post_order', true);

            if (!is_array($order)) {
                continue;
            }

            foreach ($order as $person_id) {
                foreach ($unsorted as $index => $person) {
                    if ($person['ID'] === $person_id && $person['team_id'] === $group->term_id) {
                        $sorted[] = $person;
                        unset($unsorted[$index]);
                    }
                }
            }
        }

        foreach ($unsorted as $person) {
            $sorted[] = $person;
        }

        return $sorted;
    }

    private function getSliderPeoples(): array
    {
        if ($this->getPartialData('show_people_based_on') === 'manual_posts') {
            // Still an ACF relationship field on the page builder layout, so
            // this hands back WP_Post objects.
            $manual = $this->getPartialData('manual_posts') ?: [];

            return array_map(fn($item) => PersonData::summary($item->ID), $manual);
        }

        $query = new WP_Query([
            'post_type'      => 'people',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        return array_map(
            fn($id) => PersonData::summary($id),
            wp_list_pluck($query->posts, 'ID')
        );
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
