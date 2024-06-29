<?php

/**
 *
 * Plugin Name: WP Vue API
 * Plugin URI: https://www.wordpress.org
 * Description: A WP API for vue.
 * Version: 1.0.0
 * Author: Rajan Dangi
 * Author URI: https://www.wordpress.org
 * License: GPL v3
 * Text-Domain: textdomain
 */

/**
 * No direct access allowed
 */
if (!defined('ABSPATH')) exit();


/**
 * Require Auto-Loader
 */
require_once 'vendor/autoload.php';

use WPVABR\Api\Api;

final class WP_Vue_API_BR
{

    /**
     * Define Plugin Version
     */
    const VERSION = '1.0.0';

    /**
     * Construct Function
     */
    public function __construct()
    {
        $this->plugin_constants();
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Plugin Constants
     * @since 1.0.0
     */
    public function plugin_constants()
    {
        define('WPVABR_VERSION', self::VERSION);
        define('WPVABR_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
        define('WPVABR_PLUGIN_URL', trailingslashit(plugins_url('', __FILE__)));
        define('WPVABR_NONCE', 'b?l#3&45@_*(+3&[G[xAc8O~Fv)*36*7^_$piN0.N%N~X91VbCn@.41233');
    }

    /**
     * Single tone Instance
     * @since 1.0.0
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * On Plugin Activation
     * @since 1.0.0
     */
    public function activate()
    {
        $is_installed = get_option('wpvabr_is_installed');

        if (!$is_installed) {
            update_option('wpvabr_is_installed', time());
        }

        update_option('wpvabr_is_installed', WPVABR_VERSION);
    }

    /**
     * On Plugin De-activation
     * @since 1.0.0
     */
    public function deactivate()
    {
        // On plugin deactivation
    }

    /**
     * Init Plugin
     * @since 1.0.0
     */
    public function init_plugin()
    {
        new Api();
    }
}

/**
 * Initialize Main Plugin
 * @since 1.0.0
 */
function wp_vue_api_br()
{
    return WP_Vue_API_BR::init();
}

// Run the Plugin
wp_vue_api_br();
