<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Helpers\Controller\HelpersClean;

/**
 * Clean Plugins helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class HelpersCleanPlugins extends HelpersClean
{
    /**
     * @var array
     */
    protected $available = [
            'contact-form' => true, // Contact Form 7               https://wordpress.org/plugins/contact-form-7/
            'jetpack'      => true, // Jetpack by WordPress.com     https://wordpress.org/plugins/jetpack/
            'w3tc'         => true, // W3 Total Cache               https://wordpress.org/plugins/w3-total-cache/
            'woocommerce'  => true, // WooCommerce                  https://wordpress.org/plugins/woocommerce/
            'yoast'        => true, // Yoast SEO                    https://wordpress.org/plugins/wordpress-seo/
    ];

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param array $args
     */
    public function init($args)
    {
        if (empty($args)) {
            return;
        }

        // Special case
        if (is_bool($args) && $args) {
            $args = $this->available;
        }

        // Iterate on all
        foreach ($args as $plugin => $settings) {
            $plugin = strtolower($plugin);

            if (!array_key_exists($plugin, $this->available)) {
                continue;
            }

            $plugin = str_replace('_', '-', $plugin);

            $function = Helpers::toFunctionFormat($plugin);
            $function = 'plugin'.ucfirst($function);
            $this->$function($settings);
        }
    }

    /**
     * Clean Contact Form plugin functionalities.
     *
     * @param array $settings
     */
    public function pluginContactForm($settings)
    {
        if (empty($settings)) {
            return;
        }

        $available = [
            'enqueue_styles',
        ];

        // Special case
        if (is_bool($settings) && $settings) {
            $settings = $available;
        }

        foreach ($settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_action('wp_enqueue_scripts', function () {
                    wp_localize_script('contact-form-7', 'wpcf7', [
                        'apiSettings' => [
                            'root'      => esc_url_raw(rest_url('contact-form-7/v1')),
                            'namespace' => 'contact-form-7/v1',
                        ],
                        'jqueryUi'    => 1,
                    ]);
                }, 10);
            }
        }
    }

    /**
     * Clean Jetpack plugin functionalities.
     *
     * @param array $settings
     */
    public function pluginJetpack($settings)
    {
        if (empty($settings)) {
            return;
        }

        $available = [
            'enqueue_styles', 'AtD_style', 'jetpack-carousel', 'jetpack_display_posts_widget', 'jetpack_likes',
            'jetpack_related-posts', 'jetpack-slideshow', 'jetpack-subscriptions', 'jetpack-widgets',
            'gravatar-profile-widget', 'grunion.css', 'infinity-twentyten', 'infinity-twentyeleven',
            'infinity-twentytwelve', 'noticons', 'post-by-email', 'presentations', 'publicize', 'sharedaddy',
            'sharing', 'stats_reports_css', 'tiled-gallery', 'the-neverending-homepage', 'widget-conditions',
            'widget-grid-and-list',
        ];

        // Special case
        if (is_bool($settings) && $settings) {
            $settings = $available;
        }

        foreach ($settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_filter('jetpack_implode_frontend_css', '__return_false');
            } else if (OL_ZEUS_ISADMIN) {
                wp_deregister_style($key);
            }
        }
    }

    /**
     * Clean W3TC plugin functionalities.
     *
     * @param array $settings
     */
    public function pluginW3tc($settings)
    {
        if (empty($settings)) {
            return;
        }

        $available = [
            'remove_comment',
        ];

        // Special case
        if (is_bool($settings) && $settings) {
            $settings = $available;
        }

        foreach ($settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            if ('remove_comment' === $key) {
                add_filter('w3tc_can_print_comment', '__return_false', 10, 1);
            }
        }
    }

    /**
     * Clean WooCommerce plugin functionalities.
     *
     * @param array $settings
     */
    public function pluginWoocommerce($settings)
    {
        if (empty($settings)) {
            return;
        }

        $available = [
            'enqueue_scripts', 'enqueue_styles', 'cart_fragments', 'generator_tag', 'reviews_tab',
        ];

        // Special case
        if (is_bool($settings) && $settings) {
            $settings = $available;
        }

        foreach ($settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            if ('enqueue_scripts' === $key && function_exists('is_woocommerce')) {
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
            } else if ('enqueue_styles' === $key && function_exists('is_woocommerce')) {
                add_filter('woocommerce_enqueue_styles', function ($return_false) {
                    return !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page() ? false : $return_false;
                });
            } else if ('cart_fragments' === $key && is_front_page()) {
                wp_dequeue_script('wc-cart-fragments');
            } else if ('generator_tag' === $key) {
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
            } else if ('reviews_tab' === $key) {
                add_filter('woocommerce_product_tabs', function ($tabs) {
                    unset($tabs['reviews']);
                    return $tabs;
                }, 98);
            }
        }
    }

    /**
     * Clean YOAST plugin functionalities.
     *
     * @param array $settings
     */
    public function pluginYoast($settings)
    {
        if (empty($settings) || !defined('WPSEO_VERSION')) {
            return;
        }

        $available = [
            'remove_breadcrumbs_duplicates', 'remove_comment',
        ];

        // Special case
        if (is_bool($settings) && $settings) {
            $settings = $available;
        }

        foreach ($settings as $key) {
            if (!in_array($key, $available)) {
                continue;
            }

            if ('remove_breadcrumbs_duplicates' === $key) {
                add_filter('wpseo_breadcrumb_single_link', function ($link) {
                    return false !== strpos($link, 'breadcrumb_last') ? '' : $link;
                });
            } else if ('remove_comment' === $key) {
                add_action('get_header', function () {
                    ob_start(function ($html) {
                        return preg_replace('/^\n?\<\!\-\-.*?[Y]oast.*?\-\-\>\n?$/mi', '', $html);
                    });
                });

                add_action('wp_head', function () {
                    ob_end_flush();
                }, 999);
            }
        }
    }
}
