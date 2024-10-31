<?php

require_once RESHORTZ_ROOT . '/inc/class-tgm-plugin-activation.php';


/**
 * Class Reshortz_TGM_Integration
 */
class Reshortz_TGM_Integration
{
    /**
     * Reshortz_TGM_Integration constructor.
     */
    public function __construct()
    {
        add_action( 'tgmpa_register', array($this, 'register_required_plugins') );
    }

    /**
     * Register required plugins
     */
    public function register_required_plugins()
    {
        $plugins = [
            [
                'name'     => 'Meta Box',
                'slug'     => 'meta-box',
                'required' => true,
            ]
        ];
        $config  = [
            'id' => 'reshortz',
        ];
        tgmpa( $plugins, $config );
    }
}
