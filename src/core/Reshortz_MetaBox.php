<?php

require_once RESHORTZ_ROOT . 'src/core/Reshortz_MetaWorker.php';

class Reshortz_MetaBox
{
    use Reshortz_MetaWorker;

    /**
     * Reshortz_MetaBox constructor.
     */
    public function __construct()
    {
        add_filter( 'rwmb_meta_boxes', array($this, 'register_meta_boxes') );
    }

    /**
     * Register MetaBoxes
     *
     * @param array $meta_boxes
     * @return array|mixed
     */
    public function register_meta_boxes($meta_boxes = [])
    {
        $meta_boxes[] = array(
            'title'      => 'ReShort Content',
            'post_types' => 'reshortz',
            'fields' => array(
                array(
                    'id'               => $this->makeKey('video'),
                    'name'             => esc_html__('Upload a video', 'reshortz'),
                    'type'             => 'file_upload',
                    'force_delete'     => false,
                    'max_file_uploads' => 1,
                    'mime_type'        => 'video',
                    'max_status'       => false,
                ),
                array(
                    'name' => esc_html__('Video duration (MS)', 'reshortz'),
                    'id'   => $this->makeKey('duration'),
                    'type' => 'number',
                    'std'  => 5000,
                    'min'  => 0,
                    'desc' => esc_html__('This will be used as fallback when the duration can not be loaded from meta data. Note that the player will play the whole video, this is only a reference for the progress bar.', 'reshortz'),
                ),
                array(
                    'name' => esc_html__('Go to URL', 'reshortz'),
                    'id'   => $this->makeKey('visit_url'),
                    'type' => 'url',
                ),
            )
        );

        $meta_boxes[] = array(
            'title'      => esc_html__('ReShort Analytics', 'reshortz'),
            'post_types' => 'reshortz',
            'fields' => array(
                array(
                    'type' => 'custom_html',
                    'callback' => array($this, 'render_analytics'),
                ),
            )
        );


        /**
         * Register collection fields
         */
        $meta_boxes[] = array(
            'title'      => esc_html__('ReShortz Collection', 'reshortz'),
            'post_types' => 'reshortz-collection',
            'fields' => array(
                array(
                    'name'            => esc_html__('What would you like to display?', 'reshortz'),
                    'id'              => $this->makeKey('collection_type'),
                    'type'            => 'select',
                    'options'         => array(
                        'specific'   => esc_html__('Specific Reshortz', 'reshortz'),
                        'recent'     => esc_html__('All Reshortz', 'reshortz'),
                        'category'   => esc_html__('Reshortz in Categories', 'reshortz'),
                        'tags'       => esc_html__('Reshortz having Tags', 'reshortz'),
                    ),
                    'std' => 'recent',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('How would you like them to be ordered?', 'reshortz'),
                    'id'              => $this->makeKey('order_by'),
                    'type'            => 'select',
                    'options'         => array(
                        'date_desc'  => esc_html__('Date (Newest first)', 'reshortz'),
                        'date_asc'   => esc_html__('Date (Oldest first)', 'reshortz'),
                        'likes_desc' => esc_html__('Likes (Most liked)', 'reshortz'),
                        'likes_asc'  => esc_html__('Likes (Less liked)', 'reshortz'),
                        'views_desc' => esc_html__('Views (Most viewed)', 'reshortz'),
                        'views_asc'  => esc_html__('Views (Less viewed)', 'reshortz'),
                        'comments_desc' => esc_html__('Comments (Most commented)', 'reshortz'),
                        'comments_asc' => esc_html__('Comments (Less commented)', 'reshortz'),

                    ),
                    'std' => 'date_desc',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'        => esc_html__('Select which ReShortz to include', 'reshortz'),
                    'id'          => $this->makeKey('list'),
                    'type'        => 'post',
                    'post_type'   => 'reshortz',
                    'field_type'  => 'select_advanced',
                    'placeholder' => 'Select which ReShotz to include',
                    'query_args'  => array(
                        'post_status'    => 'publish',
                        'posts_per_page' => - 1,
                    ),
                    'multiple' => true
                ),
                array(
                    'name'       => esc_html__('Reshortz Categories', 'reshortz'),
                    'id'         => $this->makeKey('q_categories'),
                    'type'       => 'taxonomy',
                    'taxonomy'   => 'reshortz-category',
                    'field_type' => 'select_advanced',
                    'multiple'   => true
                ),
                array(
                    'name'       => esc_html__('Reshortz Tags', 'reshortz'),
                    'id'         => $this->makeKey('q_tags'),
                    'type'       => 'taxonomy',
                    'taxonomy'   => 'tag',
                    'field_type' => 'select_advanced',
                    'multiple'   => true
                ),
                array(
                    'name'       => esc_html__('Total number of posts', 'reshortz'),
                    'id'         => $this->makeKey('reshortz_count'),
                    'type'       => 'text',
                    'desc'       => esc_html__('Choose how many posts you want to include. This does not apply for cases when you choose to display specific posts.', 'reshortz'),
                )
            )
        );

        $meta_boxes[] = [
            'title'      => esc_html__('UI Settings', 'reshortz'),
            'post_types' => 'reshortz-collection',
            'fields' => [
                array(
                    'name'            => esc_html__('How would you like to display the items?', 'reshortz'),
                    'id'              => $this->makeKey('display_type'),
                    'type'            => 'select',
                    'options'         => array(
                        'cards'     => esc_html__('Reshortz Cards', 'reshortz'),
                        'circles'   => esc_html__('Story Circles', 'reshortz'),
                    ),
                    'std'             => 'cards',
                    'desc'            => esc_html__('Choose the way you want to display the shortcode.', 'reshortz'),
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Choose behaviour', 'reshortz'),
                    'id'              => $this->makeKey('behavior'),
                    'type'            => 'select',
                    'options'         => array(
                        'scroll'    => esc_html__('Horizontal scroll', 'reshortz'),
                        'carousel'  => esc_html__('Carousel', 'reshortz'),
                        'grid'      => esc_html__('Simple Grid', 'reshortz'),
                    ),
                    'std'             => 'scroll',
                    'desc'            => esc_html__('Choose the way the shortcode should behave.', 'reshortz'),
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'       => esc_html__('Cards height (desktop)', 'reshortz'),
                    'id'         => $this->makeKey('cards_height_lg'),
                    'type'       => 'text',
                    'std'        => '350px',
                ),
                array(
                    'name'       => esc_html__('Cards width (desktop)', 'reshortz'),
                    'id'         => $this->makeKey('cards_width_lg'),
                    'type'       => 'text',
                    'std'        => '50%',
                ),
                array(
                    'name'       => esc_html__('Cards height (mobile)', 'reshortz'),
                    'id'         => $this->makeKey('cards_height_sm'),
                    'type'       => 'text',
                    'std'        => '350px',
                ),
                array(
                    'name'       => esc_html__('Cards width (mobile)', 'reshortz'),
                    'id'         => $this->makeKey('cards_width_sm'),
                    'type'       => 'text',
                    'std'        => '50%',
                ),
                array(
                    'name'            => esc_html__('Allow likes?', 'reshortz'),
                    'id'              => $this->makeKey('allow_likes'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'   => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Show Categories?', 'reshortz'),
                    'id'              => $this->makeKey('show_cat'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'   => esc_html__('Yes', 'reshortz'),
                        'no'    => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Show Tags?', 'reshortz'),
                    'id'              => $this->makeKey('show_tags'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'   => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Show likes count?', 'reshortz'),
                    'id'              => $this->makeKey('show_likes'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'   => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Allow comments? (depends on post settings too)', 'reshortz'),
                    'id'              => $this->makeKey('allow_comments'),
                    'type'            => 'select',
                    'desc'            => esc_html__('Will take into account comment settings at post-level.', 'reshortz'),
                    'options'         => array(
                        'yes'   => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Show comments count?', 'reshortz'),
                    'id'              => $this->makeKey('show_comments'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'    => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'yes',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
                array(
                    'name'            => esc_html__('Show views count?', 'reshortz'),
                    'id'              => $this->makeKey('show_views'),
                    'type'            => 'select',
                    'options'         => array(
                        'yes'    => esc_html__('Yes', 'reshortz'),
                        'no'     => esc_html__('No', 'reshortz'),
                    ),
                    'std'             => 'no',
                    'multiple'        => false,
                    'placeholder'     => esc_html__('Choose an option', 'reshortz'),
                ),
            ]
        ];

        $meta_boxes[] = array(
            'title'      => esc_html__('Shortcode Output', 'reshortz'),
            'post_types' => 'reshortz-collection',
            'fields' => array(
                array(
                    'type' => 'custom_html',
                    'callback' => array($this, 'render_shortcode_output'),
                ),
            )
        );


        return $meta_boxes;
    }

    public function render_shortcode_output()
    {
        ?>
        <div class="reshortz_posts-column__shortcode">
            <p title="<?php echo esc_html__('Click to copy', 'reshortz')?>">
                <code class="reshortz_shortcode_text">[reshortz_collection id=<?php echo esc_html(get_the_ID())?>]</code>
                <span class="reshortz_text_copied"><?php echo esc_html__('Copied!', 'reshortz')?></span>
            </p>
        </div>
        <?php
    }

    /**
     * Analytics page
     */
    public function render_analytics()
    {
        $item_id = get_the_ID();
        ?>
        <div class="reshortz-admin reshortz-admin__analytics_wrapper">
            <div class="reshortz-admin__analytics">
                <div class="reshortz-analytics_item">
                    <div class="reshort-analytics__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84.02L256 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 .0003 232.4 .0003 190.9L0 190.9z"/></svg>
                    </div>
                    <div class="reshort-analytics__footer">
                        <span class="reshortz-analytics_item__value"><?php echo esc_html(get_post_meta($item_id, 'reshortz_likes', true))?></span>
                    </div>
                </div>
                <div class="reshortz-analytics_item">
                    <div class="reshort-analytics__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M279.6 160.4C282.4 160.1 285.2 160 288 160C341 160 384 202.1 384 256C384 309 341 352 288 352C234.1 352 192 309 192 256C192 253.2 192.1 250.4 192.4 247.6C201.7 252.1 212.5 256 224 256C259.3 256 288 227.3 288 192C288 180.5 284.1 169.7 279.6 160.4zM480.6 112.6C527.4 156 558.7 207.1 573.5 243.7C576.8 251.6 576.8 260.4 573.5 268.3C558.7 304 527.4 355.1 480.6 399.4C433.5 443.2 368.8 480 288 480C207.2 480 142.5 443.2 95.42 399.4C48.62 355.1 17.34 304 2.461 268.3C-.8205 260.4-.8205 251.6 2.461 243.7C17.34 207.1 48.62 156 95.42 112.6C142.5 68.84 207.2 32 288 32C368.8 32 433.5 68.84 480.6 112.6V112.6zM288 112C208.5 112 144 176.5 144 256C144 335.5 208.5 400 288 400C367.5 400 432 335.5 432 256C432 176.5 367.5 112 288 112z"/></svg>
                    </div>
                    <div class="reshort-analytics__footer">
                        <span class="reshortz-analytics_item__value"><?php echo esc_html(get_post_meta($item_id, 'reshortz_views', true))?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
