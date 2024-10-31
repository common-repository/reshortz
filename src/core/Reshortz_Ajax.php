<?php
require_once RESHORTZ_ROOT . 'src/core/Reshortz_API_Adapter.php';


class Reshortz_Ajax
{
    /**
     * @var Reshortz_API_Adapter $adapter
     */
    protected $adapter;

    public function __construct()
    {
        $this->adapter = new Reshortz_API_Adapter();

        /**
         * Update video views
         */
        add_action('wp_ajax_reshortz_update_views', array($this, 'update_views'));
        add_action('wp_ajax_nopriv_reshortz_update_views', array($this, 'update_views'));

        /**
         * Like video
         */
        add_action('wp_ajax_reshortz_like_video', array($this, 'like_video'));
        add_action('wp_ajax_nopriv_reshortz_like_video', array($this, 'like_video'));

        /**
         * Unlike video
         */
        add_action('wp_ajax_reshortz_unlike_video', array($this, 'unlike_video'));
        add_action('wp_ajax_nopriv_reshortz_unlike_video', array($this, 'unlike_video'));

        /**
         * Load by tags
         */
        add_action('wp_ajax_reshortz_load_by_tags', array($this, 'load_by_tags'));
        add_action('wp_ajax_nopriv_reshortz_load_by_tags', array($this, 'load_by_tags'));

        /**
         * Load by category
         */
        add_action('wp_ajax_reshortz_load_by_cat', array($this, 'load_by_cat'));
        add_action('wp_ajax_nopriv_reshortz_load_by_cat', array($this, 'load_by_cat'));

        /**
         * Load comments and comment form (?)
         */
        add_action('wp_ajax_reshortz_load_comments', array($this, 'load_comments'));
        add_action('wp_ajax_nopriv_reshortz_load_comments', array($this, 'load_comments'));
    }

    /**
     * Load posts by tags
     */
    public function load_by_tags()
    {
        if(isset($_POST['tag_slug'])) {
            $slug = sanitize_text_field($_POST['tag_slug']);
            $args = array(
                'post_type' => 'reshortz',
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'tag',
                        'field'     => 'slug',
                        'terms'     => sanitize_title($slug)
                    )
                )
            );

            $postslist = get_posts( $args );

            $posts = array();
            foreach ($postslist as $item) {
                $data = $this->adapter->build_data($item->ID);
                if ($data) {
                    $posts[] = $data;
                }
            }

            echo json_encode(array(
                'posts' => $posts
            ));
            die();
        }
        die('Bad request');
    }

    /**
     * Load posts by cat
     */
    public function load_by_cat()
    {
        if(isset($_POST['cat_slug'])) {
            $slug = sanitize_text_field($_POST['cat_slug']);

            $args = array(
                'post_type' => 'reshortz',
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'reshortz-category',
                        'field'     => 'slug',
                        'terms'     => sanitize_title($slug)
                    )
                )
            );

            $postslist = get_posts( $args );

            $posts = array();
            foreach ($postslist as $item) {
                $data = $this->adapter->build_data($item->ID);
                if ($data) {
                    $posts[] = $data;
                }
            }

            echo json_encode(array(
                'posts' => $posts
            ));
            die();
        }

        die('Bad request');
    }

    /**
     * Update post views
     */
    public function update_views()
    {
        if(isset($_POST['post_id'])) {
            $postId = sanitize_text_field($_POST['post_id']);

            // total count
            $old_views = get_post_meta($postId, 'reshortz_views', true);
            // today count
            $today_views = get_post_meta($postId, 'reshortz_views_' . date('Y_m_d'), true);


            if(!isset($old_views) || empty($old_views)) {
                $old_views = 0;
            }


            if(!isset($today_views) || empty($today_views)) {
                $today_views = 0;
            }

            // liked items
            $views = $old_views + 1;
            $today_views = $today_views + 1;

            update_post_meta($postId, 'reshortz_views', $views );
            update_post_meta($postId, 'reshortz_views_' . date('Y_m_d'), $today_views );


            die('viewed');
        }

        die('Bad Request');
    }

    /**
     * Like a video
     */
    public function like_video()
    {
        if(isset($_POST['post_id'])) {
            $postId = sanitize_text_field($_POST['post_id']);

            // total likes
            $old_likes = get_post_meta($postId, 'reshortz_likes', true);

            // today likes
            $today_likes = get_post_meta($postId, 'reshortz_likes_' . date('Y_m_d'), true);

            if(!isset($old_likes) || empty($old_likes)) {
                $old_likes = 0;
            }

            if(!isset($today_likes) || empty($today_likes)) {
                $today_likes = 0;
            }

            // liked items
            $likes = $old_likes + 1;
            $today_likes = $today_likes + 1;

            update_post_meta($postId, 'reshortz_likes', $likes );
            update_post_meta($postId, 'reshortz_likes_' . date('Y_m_d'), $today_likes );

            die('liked');
        }

        die('Bad Request');
    }

    /**
     * Unlike a video
     */
    public function unlike_video()
    {
        if (isset($_POST['post_id'])) {
            $postId = sanitize_text_field($_POST['post_id']);


            // total likes
            $old_likes = get_post_meta($postId, 'reshortz_likes', true);

            // today likes
            $today_likes = get_post_meta($postId, 'reshortz_likes_' . date('Y_m_d'), true);

            if(!isset($old_likes) || empty($old_likes)) {
                $old_likes = 0;
            }

            if(!isset($today_likes) || empty($today_likes)) {
                $today_likes = 0;
            }

            if($old_likes - 1 >= 0) {
                $likes = $old_likes - 1;
            }

            if($today_likes - 1 >= 0 ) {
                $today_likes = $today_likes - 1;
            }

            update_post_meta($postId, 'reshortz_likes', $likes );
            update_post_meta($postId, 'reshortz_likes_' . date('Y_m_d'), $today_likes );


            die('disliked');
        }

        die('Bad Request');
    }
}
