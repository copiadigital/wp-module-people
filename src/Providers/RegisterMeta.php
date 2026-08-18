<?php

namespace People\Providers;

use People\Support\PersonData;

/**
 * Exposes the person fields to the block editor as REST-backed post meta.
 *
 * These are the same meta keys the retired ACF field group wrote to, so the
 * listing composers and Blade partials keep reading the values they always
 * did — only the editing UI changed. The editor UI that writes them lives in
 * resources/scripts/editor/PersonPanel.jsx.
 */
class RegisterMeta implements Provider
{
    public const POST_TYPE = 'people';

    public function register()
    {
        // After RegisterPostType::cpt_register(), which runs on init at the
        // default priority — register_post_meta needs the type to exist.
        add_action('init', [$this, 'registerMeta'], 11);
    }

    public function registerMeta(): void
    {
        $auth = fn() => current_user_can('edit_posts');

        register_post_meta(self::POST_TYPE, PersonData::META_POSITION, [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => $auth,
        ]);

        register_post_meta(self::POST_TYPE, PersonData::META_MANUAL_RELATED_ENABLED, [
            'type' => 'boolean',
            'single' => true,
            'default' => false,
            'show_in_rest' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback' => $auth,
        ]);

        register_post_meta(self::POST_TYPE, PersonData::META_MANUAL_RELATED, [
            'type' => 'array',
            'single' => true,
            'default' => [],
            'show_in_rest' => [
                'schema' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                ],
            ],
            'sanitize_callback' => [$this, 'sanitizeIdList'],
            'auth_callback' => $auth,
        ]);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeIdList($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('absint', $value))));
    }
}
