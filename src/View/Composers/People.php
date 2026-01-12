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

    public function with()
    {
        return [
            'teams' => $this->getGroups(),
            'peoples' => $this->getPeoples(),
        ];
    }

    public function getGroups()
    {
        $groups = get_categories([
            'taxonomy' => 'people_group',
            'hide_empty' => false,
        ]);

        return $groups;
    }

    private function getPeoples()
    {
        $style = $this->getPartialData('style');

        return $style === 'plain'
            ? $this->getPeoplesForPlainStyle()
            : $this->getPeoplesForFilteredStyle();
    }

    private function getPeoplesForPlainStyle()
    {
        global $post;

        $defaultPhoto = get_field('default_people_photo', 'option');
        $categoryIds = $this->getPartialData('groups');

        // Handle both single value and array
        if (!$categoryIds) {
            return [];
        }

        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }

        // Query people in selected categories
        $query = new WP_Query([
            'post_type'      => 'people',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => [[
                'taxonomy' => 'people_group',
                'field'    => 'term_id',
                'terms'    => $categoryIds,
            ]]
        ]);

        $unsorted = $this->buildPeopleArray($query, $defaultPhoto);

        // If single category, use its order
        if (count($categoryIds) === 1) {
            return $this->sortByGroupOrder($unsorted, $categoryIds[0]);
        }

        // For multiple categories, sort by all groups
        return $this->sortByAllGroups($unsorted);
    }

    private function getPeoplesForFilteredStyle()
    {
        $defaultPhoto = get_field('default_people_photo', 'option');

        // Query all people
        $query = new WP_Query([
            'post_type'      => 'people',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $unsorted = $this->buildPeopleArray($query, $defaultPhoto);

        return $this->sortByAllGroups($unsorted);
    }

    private function buildPeopleArray($query, $defaultPhoto)
    {
        global $post;
        $peoples = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $personTeams = wp_get_post_terms($post->ID, 'people_group');
                $photo = get_field('photo', $post->ID);

                foreach ($personTeams as $team) {
                    $peoples[] = [
                        'ID' => $post->ID,
                        'slug' => $post->post_name . '-' . $team->slug,
                        'title' => get_the_title(),
                        'position' => get_field('position', $post->ID),
                        'descriptions' => get_field('descriptions', $post->ID),
                        'photo' => $photo ?: $defaultPhoto,
                        'is_default_photo' => !$photo && $defaultPhoto,
                        'teams' => $team->slug,
                        'team_id' => $team->term_id,
                    ];
                }
            }

            wp_reset_postdata();
        }

        return $peoples;
    }

    private function sortByGroupOrder($unsorted, $groupId)
    {
        $order = get_term_meta($groupId, 'people_post_order', true);
        if (!is_array($order)) {
            $order = [];
        }

        return $this->applySortOrder($unsorted, $order, $groupId);
    }

    private function sortByAllGroups($unsorted)
    {
        $sorted = [];
        $groups = get_terms([
            'taxonomy' => 'people_group',
            'hide_empty' => false,
        ]);

        foreach ($groups as $group) {
            $order = get_term_meta($group->term_id, 'people_post_order', true);
            if (!is_array($order)) {
                $order = [];
            }

            foreach ($order as $personId) {
                foreach ($unsorted as $index => $person) {
                    if ($person['ID'] === $personId && $person['team_id'] === $group->term_id) {
                        $sorted[] = $person;
                        unset($unsorted[$index]);
                    }
                }
            }
        }

        // Add remaining unordered people
        return array_merge($sorted, $unsorted);
    }

    private function applySortOrder($unsorted, $order, $groupId = null)
    {
        $sorted = [];

        foreach ($order as $personId) {
            foreach ($unsorted as $index => $person) {
                $match = $person['ID'] === $personId;

                if ($groupId) {
                    $match = $match && $person['team_id'] === $groupId;
                }

                if ($match) {
                    $sorted[] = $person;
                    unset($unsorted[$index]);
                    break;
                }
            }
        }

        // Add remaining unordered people
        return array_merge($sorted, $unsorted);
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
