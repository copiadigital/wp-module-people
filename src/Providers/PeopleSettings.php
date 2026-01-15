<?php

namespace People\Providers;

class PeopleSettings implements Provider
{
    const OPTION_GROUP = 'people_settings';
    const OPTION_NAME = 'people_options';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function register()
    {
        //
    }

    /**
     * Add settings page as submenu under People post type
     */
    public function addSettingsPage()
    {
        add_submenu_page(
            'edit.php?post_type=people',
            __('People Settings', 'theme'),
            __('Settings', 'theme'),
            'manage_options',
            'people-settings',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Register settings
     */
    public function registerSettings()
    {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitizeOptions'],
                'default' => [
                    'enable_view_page' => false,
                ],
            ]
        );

        add_settings_section(
            'people_general_section',
            __('General Settings', 'theme'),
            [$this, 'renderSectionDescription'],
            'people-settings'
        );

        add_settings_field(
            'enable_view_page',
            __('Enable View Page', 'theme'),
            [$this, 'renderEnableViewPageField'],
            'people-settings',
            'people_general_section'
        );
    }

    /**
     * Sanitize options before saving
     */
    public function sanitizeOptions($input)
    {
        $sanitized = [];
        $sanitized['enable_view_page'] = !empty($input['enable_view_page']);

        // Flush rewrite rules when this option changes
        $old_options = get_option(self::OPTION_NAME, []);
        if (($old_options['enable_view_page'] ?? false) !== $sanitized['enable_view_page']) {
            add_action('shutdown', 'flush_rewrite_rules');
        }

        return $sanitized;
    }

    /**
     * Render section description
     */
    public function renderSectionDescription()
    {
        echo '<p>' . __('Configure the People module settings.', 'theme') . '</p>';
    }

    /**
     * Render enable view page checkbox field
     */
    public function renderEnableViewPageField()
    {
        $options = get_option(self::OPTION_NAME, []);
        $checked = !empty($options['enable_view_page']);
        ?>
        <label>
            <input type="checkbox"
                name="<?php echo esc_attr(self::OPTION_NAME); ?>[enable_view_page]"
                value="1"
                <?php checked($checked); ?>
            />
            <?php _e('Allow individual people pages to be publicly viewable', 'theme'); ?>
        </label>
        <p class="description">
            <?php _e('When enabled, each person will have their own viewable page on the frontend.', 'theme'); ?>
        </p>
        <?php
    }

    /**
     * Render the settings page
     */
    public function renderSettingsPage()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'people_messages',
                'people_message',
                __('Settings Saved', 'theme'),
                'updated'
            );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php settings_errors('people_messages'); ?>
            <form action="options.php" method="post">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections('people-settings');
                submit_button(__('Save Settings', 'theme'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Get a specific option value
     */
    public static function getOption($key, $default = null)
    {
        $options = get_option(self::OPTION_NAME, []);
        return $options[$key] ?? $default;
    }

    /**
     * Check if view page is enabled
     */
    public static function isViewPageEnabled()
    {
        return (bool) self::getOption('enable_view_page', false);
    }
}
