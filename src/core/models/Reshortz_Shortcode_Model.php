<?php

require_once RESHORTZ_ROOT . 'src/core/models/Reshortz_Model.php';

/**
 * Class Reshortz_Shortcode_Model
 */
class Reshortz_Shortcode_Model extends Reshortz_Model
{

    /**
     * @var integer $collection_id
     */
    protected $collection_id;

    /**
     * Reshortz_Shortcode_Model constructor.
     * @param $collection_id
     */
    public function __construct($collection_id)
    {
        $this->collection_id = $collection_id;
    }

    /**
     * Get UI settings for collection/shortcode
     *
     * @return array
     */
    public function get_ui_settings()
    {
        return array(
            'display_type'     => $this->get_display_type(),
            'allow_likes'      => $this->get_allow_likes(),
            'show_likes'       => $this->get_show_likes(),
            'show_comments'    => $this->get_show_comments(),
            'allow_comments'   => $this->get_allow_comments(),
            'show_cat'         => $this->get_show_cat(),
            'show_tags'        => $this->get_show_tags(),
            'show_views'       => $this->get_show_views(),
            'posts_to_display' => $this->get_reshortz_per_page(),
            'behavior'         => $this->get_behavior(),
            'cards_height_lg'     => $this->get_cards_height_lg(),
            'cards_width_lg'      => $this->get_cards_width_lg(),
            'cards_height_sm'     => $this->get_cards_height_sm(),
            'cards_width_sm'      => $this->get_cards_width_sm(),
        );
    }

    /**
     * Handle get value
     *
     * @param $key
     * @param $allowed
     * @param null $default
     * @return false|mixed
     */
    public function get_value($key, $allowed = null, $default = null)
    {
        $value = reshortz_rwmb_meta($this->makeKey($key), $this->collection_id);

        if (isset($value) && !empty($value)) {
            if(isset($allowed) && is_array($allowed) && in_array($value, $allowed)) {
                return $value;
            }

            return $value;
        }

        if(isset($default)) {
            return $default;
        }

        return false;
    }


    /**
     * Get display type
     *
     * @return false|mixed
     */
    public function get_display_type()
    {
        $allowed = [
            'cards',
            'cards2',
            'cards3',
            'circles'
        ];
        $default = 'cards';

        return $this->get_value('display_type', $allowed, $default);
    }

    /**
     * Get allow likes
     *
     * @return false|mixed
     */
    public function get_allow_likes()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('allow_likes', $allowed, $default);
    }

    /**
     * Get show likes
     *
     * @return false|mixed
     */
    public function get_show_likes()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('show_likes', $allowed, $default);
    }

    /**
     * Get show comments
     *
     * @return false|mixed
     */
    public function get_show_comments()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('show_comments', $allowed, $default);
    }

    /**
     * Get allow comments
     *
     * @return false|mixed
     */
    public function get_allow_comments()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('allow_comments', $allowed, $default);
    }

    /**
     * Get show cat
     *
     * @return false|mixed
     */
    public function get_show_cat()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('show_cat', $allowed, $default);
    }

    /**
     * Get show tags
     *
     * @return false|mixed
     */
    public function get_show_tags()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('show_tags', $allowed, $default);
    }

    /**
     * Get show views
     *
     * @return false|mixed
     */
    public function get_show_views()
    {
        $allowed = [
            'yes',
            'no'
        ];
        $default = 'yes';

        return $this->get_value('show_views', $allowed, $default);
    }

    /**
     * Get show views
     *
     * @return false|mixed
     */
    public function get_reshortz_count()
    {
        $default = get_option('posts_per_page');
        $value   = reshortz_rwmb_meta('reshortz_reshortz_count', $this->collection_id);

        return isset($value) && !empty($value) ? (int)$value : (int)$default;
    }

    /**
     * @return false|mixed
     */
    public function get_reshortz_per_page()
    {
        $default = get_option('posts_per_page');
        $value   = reshortz_rwmb_meta('reshortz_reshortz_per_page', $this->collection_id);

        return isset($value) && !empty($value) ? (int)$value : (int)$default;
    }

    /**
     * @return false|mixed
     */
    public function get_behavior()
    {
        $allowed = [
            'scroll',
            'carousel',
            'grid'
        ];
        $default = 'scroll';

        return $this->get_value('behavior', $allowed, $default);
    }

    /**
     * @return false|mixed
     */
    public function get_cards_height_lg()
    {
       return $this->get_value('cards_height_lg', null, '350px');
    }

    /**
     * @return false|mixed
     */
    public function get_cards_width_lg()
    {
        return $this->get_value('cards_width_lg', null, '50%');
    }

    /**
     * @return false|mixed
     */
    public function get_cards_height_sm()
    {
        return $this->get_value('cards_height_sm', null, '350px');
    }

    /**
     * @return false|mixed
     */
    public function get_cards_width_sm()
    {
        return $this->get_value('cards_width_sm', null, '50%');
    }
}
