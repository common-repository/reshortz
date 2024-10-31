<?php

/**
 * Class Reshortz_AssetManager
 *
 * Responsible for loading assets and managing front-end assets
 */
class Reshortz_AssetManager
{
    /**
     * Reshortz_AssetManager constructor.
     */
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    /**
     * Register required scripts and styles.
     */
    public function enqueue_scripts()
    {
        wp_register_script('vue', RESHORTZ_ASSET_URL . '/js/vue.min.js', '', true);
        wp_register_style( 'reshortz-styles', RESHORTZ_ASSET_URL . '/css/reshortz.css', [], '' );
        wp_register_script( 'reshortz-collection', RESHORTZ_ASSET_URL . '/js/reshortz-collection.js', ['jquery', 'vue'], '', true );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts()
    {
        if(is_admin()) {
            wp_enqueue_style('reshortz-admin-css', RESHORTZ_ASSET_URL . '/css/admin.css', [], '');
            wp_enqueue_script('reshortz-admin-js', RESHORTZ_ASSET_URL . '/js/admin.js', ['jquery'], '', true);
        }
    }
}
