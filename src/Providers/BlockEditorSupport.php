<?php

namespace People\Providers;

/**
 * People is a Gutenberg-only post type with a deliberately minimal canvas.
 *
 * The biography is written in the content area, which defaults to allowing
 * only `core/paragraph`. Everything else about a person (position, photo,
 * related team) lives in the Person Details sidebar panel.
 *
 * Since the ACF field group is gone, People must not be switchable back to the
 * classic editor, so these filters keep it in wp-module-blocks'
 * `theme_block_editor_post_types` list whether or not the option row exists.
 * The Block Editor → Post Types screen will therefore always show People
 * ticked, and unticking it will not stick.
 */
class BlockEditorSupport implements Provider
{
    public const POST_TYPE = 'people';
    public const OPTION_KEY = 'theme_block_editor_post_types';
    public const ALLOWED_BLOCKS_OPTION = 'theme_block_editor_allowed_blocks';

    public function register()
    {
        add_filter('option_' . self::OPTION_KEY, [$this, 'forceEnabled']);
        add_filter('default_option_' . self::OPTION_KEY, [$this, 'forceEnabled']);

        // The locked hero block every other Gutenberg post type gets makes no
        // sense on a person profile — the photo comes from the sidebar panel.
        add_filter('theme/post_types_skip_hero_template', function ($skip) {
            $skip[] = self::POST_TYPE;

            return $skip;
        });

        // A biography is prose, so People starts with Paragraph and nothing
        // else instead of the theme-wide default block list. This only seeds
        // the default — Block Editor → Allowed Blocks stays authoritative.
        // Offer theme/people as a tickable block on the Allowed Blocks screen.
        add_filter('theme/manageable_blocks', function ($blocks) {
            $blocks[] = 'theme/people';

            return $blocks;
        });

        add_filter('option_' . self::ALLOWED_BLOCKS_OPTION, [$this, 'defaultToParagraph']);
        add_filter('default_option_' . self::ALLOWED_BLOCKS_OPTION, [$this, 'defaultToParagraph']);
    }

    /**
     * Seed the People row of the Allowed Blocks setting with Paragraph only.
     *
     * Once an admin saves the People tab their selection is stored and this
     * no-ops — including a deliberately empty selection.
     *
     * @param mixed $value
     * @return array<string, string[]>
     */
    public function defaultToParagraph($value): array
    {
        $value = is_array($value) ? $value : [];

        if (!array_key_exists(self::POST_TYPE, $value)) {
            $value[self::POST_TYPE] = ['core/paragraph'];
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function forceEnabled($value): array
    {
        $types = is_array($value) ? $value : ['post'];

        if (!in_array(self::POST_TYPE, $types, true)) {
            $types[] = self::POST_TYPE;
        }

        return array_values($types);
    }
}
