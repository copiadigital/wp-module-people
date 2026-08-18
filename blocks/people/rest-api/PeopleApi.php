<?php

namespace People\Blocks\RestApi;

use People\Blocks\PeopleBlockData;

/**
 * `/wp-json/blocks/v1/people` — the groups and people the theme/people block
 * renders. The block ships static markup and fills it in from here on load,
 * matching how the news and latest-news blocks work.
 */
class PeopleApi
{
    private static $singleton;

    public static function register(): void
    {
        if (! self::$singleton) {
            self::$singleton = new self;
        }
    }

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_api_urls']);
    }

    public function register_routes(): void
    {
        register_rest_route('blocks/v1', '/people', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [$this, 'handle'],
            'args' => [
                'style' => ['type' => 'string', 'default' => 'tabs'],
                'groups' => ['type' => 'string', 'default' => ''],
                'based_on' => ['type' => 'string', 'default' => 'default'],
                'ids' => ['type' => 'string', 'default' => ''],
            ],
        ]);
    }

    public function handle(\WP_REST_Request $request): array
    {
        $toIds = fn($csv) => array_values(array_filter(array_map(
            'intval',
            array_filter(explode(',', (string) $csv), 'strlen')
        )));

        $attributes = [
            'style' => (string) $request->get_param('style'),
            'groups' => $toIds($request->get_param('groups')),
            'showPeopleBasedOn' => (string) $request->get_param('based_on'),
            'manualPosts' => $toIds($request->get_param('ids')),
        ];

        $groups = array_map(fn($term) => [
            'id' => (int) $term->term_id,
            'name' => $term->name,
        ], PeopleBlockData::groups($attributes));

        $people = array_map(function ($person) {
            return [
                // Unique per row: a person repeats once per group they are in.
                'key' => $person['ID'] . '-' . ($person['group_id'] ?? 0),
                'id' => (int) $person['ID'],
                'groupId' => (int) ($person['group_id'] ?? 0),
                'name' => $person['title'],
                'position' => $person['position'],
                'description' => $person['descriptions'],
                'photo' => (string) ($person['photo']['url'] ?? ''),
                'hasPhoto' => ! empty($person['photo']['url']),
                'link' => $person['link'],
            ];
        }, PeopleBlockData::people($attributes));

        return ['groups' => $groups, 'people' => $people];
    }

    /**
     * Hand the endpoint URL to view.js rather than hardcoding it there.
     */
    public function enqueue_api_urls(): void
    {
        if (! has_block('theme/people')) {
            return;
        }

        wp_register_script('theme-people-api', false, [], null, true);
        wp_enqueue_script('theme-people-api');
        wp_add_inline_script(
            'theme-people-api',
            'window.THEME_PEOPLE_API = ' . wp_json_encode([
                'people' => rest_url('blocks/v1/people'),
            ]) . ';',
            'before'
        );
    }
}

PeopleApi::register();
