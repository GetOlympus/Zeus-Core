<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Clean WooCommerce Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/woocommerce/
 *
 */

class HelpersPluginWoocommerce
{
    /**
     * @var array
     */
    protected $available = [
        'enqueue_scripts', 'enqueue_styles', 'cart_fragments', 'generator_tag', 'reviews_tab',
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

            if ('enqueue_scripts' === $key && function_exists('is_woocommerce') && !OL_ZEUS_ISADMIN) {
                add_action('wp_print_scripts', function () {
                    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
                        wp_dequeue_script('wc-add-to-cart');
                        wp_dequeue_script('woocommerce');
                        wp_dequeue_script('wc-cart-fragments');
                    }
                }, 100);

                add_action('wp_enqueue_scripts', function () {
                    if (!is_woocommerce()) {
                        remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));
                    }
                }, 99);

                add_action('wp_enqueue_scripts', function () {
                    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
                        wp_dequeue_style('woocommerce-layout');
                        wp_dequeue_style('woocommerce-smallscreen');
                        wp_dequeue_style('woocommerce-general');
                    }
                });

                continue;
            }

            if ('enqueue_styles' === $key && function_exists('is_woocommerce') && !OL_ZEUS_ISADMIN) {
                add_filter('woocommerce_enqueue_styles', function ($r_false) {
                    return !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page() ? false : $r_false;
                });

                continue;
            }

            if ('cart_fragments' === $key && !OL_ZEUS_ISADMIN && is_front_page()) {
                add_action('wp_print_styles', function () {
                    wp_dequeue_script('wc-cart-fragments');
                });

                continue;
            }

            if ('generator_tag' === $key && !OL_ZEUS_ISADMIN) {
                add_action('get_header', function () {
                    remove_action('wp_head', 'wc_generator_tag');

                    if (isset($GLOBALS['woocommerce']) && is_object($GLOBALS['woocommerce'])) {
                        remove_action('wp_head', [$GLOBALS['woocommerce'], 'generator']);
                    }
                });

                add_action('woocommerce_init', function () {
                    remove_action('wp_head', 'wc_generator_tag');

                    if (isset($GLOBALS['woocommerce']) && is_object($GLOBALS['woocommerce'])) {
                        remove_action('wp_head', [$GLOBALS['woocommerce'], 'generator']);
                    }
                });

                continue;
            }

            if ('reviews_tab' === $key && !OL_ZEUS_ISADMIN) {
                add_filter('woocommerce_product_tabs', function ($tabs) {
                    unset($tabs['reviews']);
                    return $tabs;
                }, 98);

                continue;
            }
        }
    }
}
