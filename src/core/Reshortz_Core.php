<?php

/**
 * Class Reshortz_Core
 *
 * Handles misc core functionalities
 */
class Reshortz_Core
{
    /**
     * Reshortz_Core constructor.
     */
    public function __construct()
    {
        $this->add_image_sizes();

        add_filter('body_class', array($this, 'filter_body_classes'));
        add_action('admin_notices', array($this, 'reshortz_admin_notice'));
    }

    public function reshortz_admin_notice()
    {
            ?>
            <div class="notice notice-warning is-dismissible">
             <p><strong> <?php echo esc_html__('ReShortz PRO comes with even more features!', 'reshortz') ?></strong></p>
                <ul class="reshortz-features">
                    <li><?php echo esc_html__('Built-in pre-roll video ads', 'reshortz')?></li>
                    <li><?php echo esc_html__('Pagination for ReShortz Cards', 'reshortz')?></li>
                    <li><?php echo esc_html__('Built-in analytics', 'reshortz')?></li>
                    <li><?php echo esc_html__('Users can comment on videos', 'reshortz')?></li>
                    <li><?php echo esc_html__('Developer Hooks', 'reshortz')?></li>
                </ul>
             <p><a href="http://figaro.tech/product/reshortz-pro/" class="btn-purchase-reshortz-pro"><?php echo esc_html__('Buy PRO', 'reshortz') ?></a></p>
         </div>

        <?php
    }


    /**
     * @param array $classes
     * @return array|mixed
     */
    public function filter_body_classes($classes = array())
    {
        $classes[] = 'reshortz-context';
        return $classes;
    }

    /**
     * Add image sizes
     */
    public function add_image_sizes()
    {
        add_image_size('reshortz_image_ad_mobile', 375, 812, true);
        add_image_size('reshortz_image_ad_desktop', 812, 375, false);
    }
}
