<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Clean Google Tag Manager for WordPress Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.10
 * @see        https://wordpress.org/plugins/duracelltomi-google-tag-manager/
 *
 */

class HelpersPluginGoogleTagManager
{
    /**
     * @var array
     */
    protected $available = [
        'enqueue_scripts',
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
        if (empty($settings)) {
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

            if ('enqueue_scripts' === $key && !OL_ZEUS_ISADMIN) {
                add_filter('gtm4wp_event-outbound', '__return_true');
                add_filter('gtm4wp_event-form-move', '__return_true');
                add_filter('gtm4wp_event-social', '__return_true');
                add_filter('gtm4wp_event-email-clicks', '__return_true');
                add_filter('gtm4wp_event-downloads', '__return_true');
                add_filter('gtm4wp_scroller-enabled', '__return_true');

                if (function_exists('gtm4wp_wp_footer')) {
                    remove_action('wp_footer', 'gtm4wp_wp_footer');
                    add_action('wp_footer', 'gtm4wp_wp_footer', 999);
                }

                if (function_exists('gtm4wp_woocommerce_wp_footer')) {
                    remove_action('wp_footer', 'gtm4wp_woocommerce_wp_footer');
                    add_action('wp_footer', 'gtm4wp_woocommerce_wp_footer',500);
                }

                continue;
            }
        }
    }
}
