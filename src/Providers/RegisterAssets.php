<?php

namespace People\Providers;

use Illuminate\Support\Facades\Vite;
use People\Providers\PeopleSettings;

class RegisterAssets implements Provider
{
    private const ENTRY = 'modules/wp-module-people/resources/scripts/people-editor.js';

    public function __construct()
    {
        add_action('enqueue_block_editor_assets', [$this, 'enqueueEditorAssets']);
        add_action('admin_enqueue_scripts', [$this, 'hideGroupParentField']);
    }

    public function register()
    {
        //
    }

    /**
     * Loads the Person Details sidebar panel and registers the theme/people
     * block. Needed on every block editor screen, not just the People post
     * type, because any page may insert the block — the panel hides itself
     * elsewhere.
     */
    public function enqueueEditorAssets()
    {
        if (!Vite::isRunningHot()) {
            foreach ($this->entryDependencies() as $dependency) {
                if (!wp_script_is($dependency)) {
                    wp_enqueue_script($dependency);
                }
            }
        }

        echo Vite::withEntryPoints([self::ENTRY])->toHtml();

        printf(
            '<script>window.THEME_PEOPLE_VIEW_PAGE_ENABLED = %s;</script>',
            PeopleSettings::isViewPageEnabled() ? 'true' : 'false'
        );

        // Groups are a flat list (RegisterPostType::forceFlatParent), so drop
        // the parent selector from the "Add New Group" form. It is the only
        // <select> in that form, sitting after the new-term name input.
        self::inlineStyle(
            'people-editor',
            '.editor-post-taxonomies__hierarchical-terms-input ~ .components-base-control:has(select) { display: none; }'
        );
    }

    /**
     * Same idea for the Groups admin screen, which renders a "Parent Group"
     * dropdown on both the add-new form and the term edit form.
     */
    public function hideGroupParentField($hook): void
    {
        if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) {
            return;
        }

        if (($_GET['taxonomy'] ?? '') !== RegisterPostType::TAXONOMY) {
            return;
        }

        self::inlineStyle('people-terms', '.term-parent-wrap { display: none; }');
    }

    /**
     * Print CSS under a handle of our own. Attaching to a core handle only
     * works when that handle happens to be enqueued on the screen.
     */
    private static function inlineStyle(string $handle, string $css): void
    {
        if (!wp_style_is($handle, 'registered')) {
            wp_register_style($handle, false, [], null);
        }

        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }

    /**
     * WordPress script handles the editor entry expects on the page.
     *
     * @roots/vite-plugin externalises every `@wordpress/*` import to a `wp.*`
     * global and writes one theme-wide `editor.deps.json` for the whole
     * build, so there is no per-entry list to read — this mirrors the imports
     * in resources/scripts/editor/PersonPanel.jsx and must be kept in step
     * with them.
     */
    private function entryDependencies(): array
    {
        return [
            'wp-plugins',
            'wp-blocks',
            'wp-editor',
            'wp-block-editor',
            'wp-components',
            'wp-core-data',
            'wp-data',
            'wp-element',
            'wp-i18n',
        ];
    }
}
