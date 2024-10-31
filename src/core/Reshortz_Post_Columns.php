<?php


class Reshortz_Post_Columns
{
    /**
     * Reshortz_Post_Columns constructor.
     */
    public function __construct()
    {
        // Add columns to shortcodes
        add_action('manage_reshortz-collection_posts_columns', array($this, 'shortcode_columns'));
        add_action('manage_reshortz-collection_posts_custom_column', array($this, 'render_shortcode_column'), 10, 2);

        // Add columns to posts
        add_action('manage_reshortz_posts_columns', array($this, 'reshorts_columns'));
        add_action('manage_reshortz_posts_custom_column', array($this, 'render_reshorts_column'), 10, 2);

        // Make posts columns sortable
        add_filter( 'manage_edit-reshortz_sortable_columns', array($this, 'reshortz_sortable_columns'));

        // Handle sorting
        add_action( 'pre_get_posts', array($this, 'reshortz_handle_orderby') );
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function reshortz_sortable_columns($columns)
    {
        $columns['reshortz_likes'] = 'reshortz_likes';
        $columns['reshortz_views'] = 'reshortz_views';
        return $columns;
    }

    /**
     * Handle admin order by
     *
     * @param $query
     */
    public function reshortz_handle_orderby($query)
    {
        if( ! is_admin() ) {
            return;
        }

        $orderby = $query->get( 'orderby');

        if( $orderby === 'reshortz_likes' ) {
            $query->set('meta_key','reshortz_likes');
            $query->set('orderby','meta_value_num');
        }

        if( $orderby === 'reshortz_views' ) {
            $query->set('meta_key','reshortz_views');
            $query->set('orderby','meta_value_num');
        }
    }

    /**
     * Add columns to reshortz post type
     *
     * @param $columns
     * @return mixed
     */
    public function reshorts_columns($columns)
    {
        $columns['reshortz_likes'] = esc_html__('Likes', 'reshortz');
        $columns['reshortz_views'] = esc_html__('Views', 'reshortz');

        return $columns;
    }

    /**
     * @param $column
     * @param $post_id
     */
    public function render_reshorts_column($column, $post_id)
    {
        if($column === 'reshortz_likes') {
            $likes = get_post_meta($post_id, 'reshortz_likes', true);
            echo $likes ? esc_html($likes) : '0';
        }

        if($column === 'reshortz_views') {
            $views = get_post_meta($post_id, 'reshortz_views', true);
            echo $views ? esc_html($views) : '0';
        }
    }

    /**
     * @param $columns
     * @return array
     */
    public function shortcode_columns($columns)
    {
        if(isset($columns['date'])) {
            unset($columns['date']);
        }

        $columns['reshortz_shortcode'] = esc_html__('Shortcode', 'reshortz');
        return $columns;
    }

    /**
     * Display shortcode
     *
     * @param $column
     * @param $post_id
     */
    public function render_shortcode_column($column, $post_id)
    {
        if($column === 'reshortz_shortcode') {
            ?>
                <div class="reshortz_posts-column__shortcode">
                    <p title="<?php echo esc_html__('Click to copy', 'reshortz')?>">
                        <code class="reshortz_shortcode_text">[reshortz_collection id=<?php echo esc_html($post_id)?>]</code>
                        <span class="reshortz_text_copied"><?php echo esc_html__('Copied!', 'reshortz')?></span>
                    </p>
                </div>
            <?php
        }
    }
}
