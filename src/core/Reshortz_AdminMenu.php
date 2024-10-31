<?php

/**
 * Class Reshortz_AdminMenu
 *
 * All things related to admin panel menus.
 *
 */
class Reshortz_AdminMenu
{
    /**
     * Reshortz_AdminMenu constructor.
     */
    public function __construct()
    {
        if(is_admin()) {
            add_action('admin_menu', array($this, 'register_admin_menu'));
        }
    }

    /**
     * Register admin menus
     */
    public function register_admin_menu()
    {
        /**
         * Move ReShortz collections under Reshortz
         */
        add_submenu_page(
            'edit.php?post_type=reshortz',
            esc_html__('Manage Shortcodes', 'reshortz'),
            esc_html__('Manage Shortcodes', 'reshortz'),
            'manage_options',
            'edit.php?post_type=reshortz-collection');
    }

    /**
     * Render settings page
     */
    public function render_settings_page()
    {
        require_once RESHORTZ_ROOT . 'views/admin/settings.php';
        return;
    }
}
