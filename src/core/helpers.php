<?php
/**
 * Defines various helper classes
 */

if ( ! function_exists( 'reshortz_rwmb_meta' ) ) {

    /**
     * Abstraction layer over rwmb_meta,
     * Can be used to define some default, fallback behaviours.
     *
     * @param $key
     * @param null $post_id
     * @param array $args
     * @return mixed|null
     */
    function reshortz_rwmb_meta( $key,  $post_id = null, $args = [] ) {
        if(function_exists('rwmb_meta')) {
            return rwmb_meta($key, $args, $post_id);
        }
        return null;
    }
}

if( ! function_exists('reshortz_rwmb_meta_url') ) {
    function reshortz_rwmb_meta_url( $key,  $post_id = null, $args = [] ) {
        $res = reshortz_rwmb_meta($key, $post_id, $args);
        if(isset($res) && is_array($res)) {
            $res = reset($res);
            if(isset($res['url'])) {
                return $res['url'];
            }
        }

        return  null;
    }
}
