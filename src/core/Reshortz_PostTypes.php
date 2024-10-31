<?php

/**
 * Class Reshortz_PostTypes
 *
 * All things related to Custom Post Types.
 */
class Reshortz_PostTypes
{
    /**
     * Reshortz_PostTypes constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('save_post', array($this, 'on_reshortz_save'));
    }

    /**
     * Add default likes & views
     *
     * @param $post_id
     */
    public function on_reshortz_save($post_id)
    {
        if(get_post_type($post_id) === 'reshortz') {
            add_post_meta($post_id, 'reshortz_likes', 0); // likes start from 0
            add_post_meta($post_id, 'reshortz_views', 1); // views start from at least 1
        }
    }

    /**
     * Handle the registration of post types.
     */
    public function register_post_types()
    {
        $reshortz_labels = array(
            'name'               => esc_html__( 'ReShortz Posts', 'reshortz' ),
            'singular_name'      => esc_html__( 'ReShort Post', 'reshortz' ),
            'add_new'            => esc_html__( 'Add New', 'reshortz' ),
            'add_new_item'       => esc_html__( 'Add New ReShort', 'reshortz' ),
            'edit_item'          => esc_html__( 'Edit ReShort', 'reshortz' ),
            'new_item'           => esc_html__( 'New ReShort', 'reshortz' ),
            'all_items'          => esc_html__( 'All ReShorts', 'reshortz' ),
            'view_item'          => esc_html__( 'View ReShort', 'reshortz' ),
            'search_items'       => esc_html__( 'Search ReShort', 'reshortz' ),
            'not_found'          => esc_html__( 'No ReShorts found', 'reshortz' ),
            'not_found_in_trash' => esc_html__( 'No ReShorts found in the Trash', 'reshortz' ),
            'menu_name'          => esc_html__('ReShortz', 'reshortz'),
        );

        $reshortz_args = array(
            'labels'        => $reshortz_labels,
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
            'has_archive'   => true,
            'menu_icon'     => 'dashicons-video-alt3'
        );

        $reshortz_collection_labels = array(
            'name'               => esc_html__( 'Reshort Shortcodes', 'reshortz' ),
            'singular_name'      => esc_html__( 'Reshort Shortcode', 'reshortz' ),
            'add_new'            => esc_html__( 'Add New', 'reshortz' ),
            'add_new_item'       => esc_html__( 'Add New Reshort Shortcode', 'reshortz' ),
            'edit_item'          => esc_html__( 'Edit Reshort Shortcode', 'reshortz' ),
            'new_item'           => esc_html__( 'New Reshort Shortcode', 'reshortz' ),
            'all_items'          => esc_html__( 'All Reshort Shortcodes', 'reshortz' ),
            'view_item'          => esc_html__( 'View Reshort Shortcode', 'reshortz' ),
            'search_items'       => esc_html__( 'Search Reshort Shortcodes', 'reshortz' ),
            'not_found'          => esc_html__( 'No reshortz found', 'reshortz' ),
            'not_found_in_trash' => esc_html__( 'No reshortz found in the Trash', 'reshortz' ),
            'menu_name'          => esc_html__('Reshort Shortcodes', 'reshortz'),
        );

        $reshortz_collection_args = array(
            'labels'        => $reshortz_collection_labels,
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' ),
            'has_archive'   => true,
            'show_in_menu'       => 'admin.php?page=reshortz',
        );

        register_post_type( 'reshortz', $reshortz_args );
        register_post_type( 'reshortz-collection', $reshortz_collection_args );
    }

    /**
     * Register and link taxonomies to post types.
     */
    public function register_taxonomies()
    {
        $labels = array(
            'name'              => esc_html__( 'ReShortz Categories', 'reshortz' ),
            'singular_name'     => esc_html__( 'ReShort Category', 'reshortz' ),
            'search_items'      => esc_html__( 'Search ReShortz Categories', 'reshortz' ),
            'all_items'         => esc_html__( 'All ReShortz Categories', 'reshortz' ),
            'parent_item'       => esc_html__( 'Parent ReShort Category', 'reshortz' ),
            'parent_item_colon' => esc_html__( 'Parent ReShort Category', 'reshortz' ),
            'edit_item'         => esc_html__( 'Edit ReShort Category', 'reshortz' ),
            'update_item'       => esc_html__( 'Update ReShort Category', 'reshortz' ),
            'add_new_item'      => esc_html__( 'Add New ReShort Category', 'reshortz' ),
            'new_item_name'     => esc_html__( 'New ReShort Category Name', 'reshortz' ),
            'menu_name'         => esc_html__( 'Categories', 'reshortz' ),
        );

        register_taxonomy('reshortz-category', array('reshortz'), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'type' ),
        ));

        register_taxonomy('tag','reshortz',array(
            'hierarchical'          => false,
            'show_ui'               => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'tag' ),
        ));
    }
}
