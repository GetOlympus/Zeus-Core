<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Clean WP Rocket for WordPress Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.10
 * @see        https://wp-rocket.me/
 *
 */

class HelpersPluginWpRocket
{
    /**
     * @var array
     */
    protected $available = [
        'enqueue_scripts', 'plugin_name',
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor.
     *
     * @param  array   $settings
     */
    public function __construct($settings)
    {
        if (empty($settings) || !defined('WP_ROCKET_VERSION')) {
            return;
        }

        $this->settings = $settings;

        // Initialize
        $this->init();
    }

    /**
     * Initialize main functions.
     */
    public function init()
    {
        // Special case
        if (is_bool($this->settings) && $this->settings) {
            $this->settings = $this->available;
        }

        // Iterate
        foreach ($this->settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $this->available)) {
                continue;
            }

            if ('enqueue_scripts' === $key) {
                add_action('after_setup_theme', function () {
                    if (function_exists('__rocket_insert_minify_js_in_footer')) {
                        remove_action('wp_footer', '__rocket_insert_minify_js_in_footer', PHP_INT_MAX);
                        add_action('wp_footer', '__rocket_insert_minify_js_in_footer', 20);
                    }
                }, 0);

                apply_filters('rocket_minify_debug', '__return_true');

                continue;
            }

            if ('plugin_name' === $key) {
                add_filter('get_rocket_option_wl_plugin_name', function () {
                    return Translate::t('helpers.plugin.wprocket.pluginname');
                });

                continue;
            }
        }
    }
}
