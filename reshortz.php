<?php

/**
 * Plugin Name: ReShortz - Video Stories for WordPress
 * Plugin URI: http://figaro.tech/
 * Description: With ReShortz you can create inspiring video stories which your users can view in a app-like experience.
 * Version: 1.0.2
 * Author: FigaroTech
 * Author URI: http://figaro.tech/
 * License: GPL2+
 * Text Domain: reshortz
 */


define('RESHORTZ_ROOT',  plugin_dir_path( __FILE__ ));
define('RESHORTZ_ASSET_URL',  plugin_dir_url( __FILE__ ) . 'assets');
define('RESHORTZ_VIEWS_PATH', RESHORTZ_ROOT  . 'views');


/**
 * Require core loader
 */
require_once RESHORTZ_ROOT . '/src/core/bootstrap.php';


/**
 * Require Shortcodes loader
 */
require_once RESHORTZ_ROOT . '/src/shortcodes/bootstrap.php';
