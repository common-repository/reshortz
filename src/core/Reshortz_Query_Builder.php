<?php

require_once RESHORTZ_ROOT . 'src/core/Reshortz_MetaWorker.php';

class Reshortz_Query_Builder
{
    use Reshortz_MetaWorker;

    /**
     * Get shortcode items
     *
     * @param Reshortz_Shortcode_Model $model
     * @param $collection_id
     * @return array|mixed|null
     */
    public function get_shortcode_items($collection_id, $model)
    {
        $items      = [];

        $type       = reshortz_rwmb_meta($this->makeKey('collection_type'), $collection_id);
        $order_by   = reshortz_rwmb_meta($this->makeKey('order_by'), $collection_id);
        $categories = reshortz_rwmb_meta($this->makeKey('q_categories'), $collection_id);
        $tags       = reshortz_rwmb_meta($this->makeKey('q_tags'), $collection_id);
        $total_posts_count = $model->get_reshortz_count();

        if($type === 'specific') {
            $items = reshortz_rwmb_meta($this->makeKey('list'), $collection_id);
        } elseif ($type === 'recent') {
            $query = $this->smart_get_posts(array(
                'order'     => $this->get_order_type($order_by),
                'order_by'  => $this->get_order_by($order_by),
                'posts_per_page' => $total_posts_count,
            ));
            $items = $query->get_posts();
        } elseif ($type === 'category') {

            $query = $this->smart_get_posts(array(
                'tax'       => array_map(function($cat){ return $cat->term_id; }, $categories),
                'order'     => $this->get_order_type($order_by),
                'order_by'  => $this->get_order_by($order_by),
                'posts_per_page' => $total_posts_count,
            ));

            $items = $query->get_posts();
        } elseif ($type === 'tags') {

            $query = $this->smart_get_posts(array(
                'tax'       => array_map(function($cat){ return $cat->term_id; }, $tags),
                'order'     => $this->get_order_type($order_by),
                'order_by'  => $this->get_order_by($order_by),
                'posts_per_page' => $total_posts_count,
                'tax_name'  => 'tag'
            ));

            $items = $query->get_posts();
        }

        return $items;
    }

    /**
     * Get order by param
     *
     * @param $order_by
     * @return string
     */
    public function get_order_by($order_by)
    {
        switch ($order_by) {
            case 'date_desc':
            case 'date_asc':
                return 'date';
            break;

            case 'likes_desc':
            case 'likes_asc':
                return 'likes';
            break;

            case 'views_desc':
            case 'views_asc':
                return 'views';
            break;
            case 'comments_desc':
            case 'comments_asc':
                return 'comments';
            break;

            default:
                return 'date';
        }
    }

    /**
     * Get ASC or DESC order type
     *
     * @param $order_by
     * @return string
     */
    public function get_order_type($order_by)
    {
        switch ($order_by) {
            case 'date_desc':
            case 'likes_desc':
            case 'comments_desc':
            case 'views_desc':
                return 'DESC';
            break;
            case 'date_asc':
            case 'likes_asc':
            case 'comments_asc':
            case 'views_asc':
                return 'ASC';
                break;
            default:
                return 'DESC';
        }
    }

    /**
     * Build WP_Query from options
     *
     * @param array $atts
     * @return WP_Query
     */
    public function smart_get_posts( $atts = array() )
    {
        $post_type = 'reshortz';
        $tax = $this->get_tax( $post_type );

        $args['post_type'] = $post_type;

        if ( ! empty( $atts[ 'tax' ] ) ) {

            $args['tax_query'] = array(

                array(
                    'taxonomy' => isset($atts['tax_name']) ? $atts['tax_name'] : $tax,
                    'field'    => 'id',
                    'terms'    => $atts[ 'tax' ]
                )
            );
        }

        $order    = isset( $atts['order'] ) ? strtoupper( $atts['order'] ) : 'DESC';
        $order_by = isset( $atts['order_by'] )  ? $atts['order_by'] : 'post_date';

        $args['order'] = $order;
        $args['post_status'] = 'publish';

        if(isset($atts['posts_per_page'])) {
            $args['posts_per_page'] = $atts['posts_per_page'];
        }

        $args = $this->order_by( $order_by, $args );
        $query = new WP_Query( $args );

        return $query;
    }

    /**
     * @param string $order_by
     * @param array $args
     * @param string $featured
     * @return array|mixed
     */
    public function order_by( $order_by = 'date', $args = array(), $featured = 'n')
    {
        $order_variants = array('date', 'comments', 'views', 'likes', 'start-date' , 'post_date');

        $order_by = (in_array($order_by, $order_variants)) ? $order_by : 'post_date' ;

        if( $featured === 'y' ){
            $args['meta_query'] = array(
                array(
                    'key' => 'featured',
                    'value' => 'yes',
                    'compare' => '=',
                ),
            );
        }

        if( $order_by == 'date' ) {
            $args['orderby'] = 'post_date';
        }

        if( $order_by === 'comments' ){
            $args['orderby'] = 'comment_count';
        }

        if( $order_by === 'views' ){
            $args['meta_key'] = 'reshortz_views';
            $args['orderby']  = 'meta_value_num';
        }

        if( $order_by === 'likes' ){
            $args['meta_key'] = 'reshortz_likes';
            $args['orderby']  = 'meta_value_num';
        }

        if( $order_by === 'views' ){
            $args['meta_key'] = 'reshortz_views';
            $args['orderby']  = 'meta_value_num';
        }

        return $args;
    }


    /**
     * Get taxonomy
     *
     * @param $post_type
     * @return string
     */
    public function get_tax( $post_type )
    {
        $tax = 'category';

        if ( 'post' == $post_type ) {
            $tax = 'category';
        } elseif ( 'reshortz' == $post_type ) {
            $tax = 'reshortz-category';
        }

        return $tax;
    }

}
