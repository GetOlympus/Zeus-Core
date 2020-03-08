<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins;

/**
 * Clean Jetpack Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners\Plugins
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/jetpack/
 *
 */

class JetpackPlugin
{
    /**
     * @var array
     */
    protected $available = [
        'default', 'enqueue_styles', 'AtD_style', 'jetpack-carousel', 'jetpack_display_posts_widget', 'jetpack_likes',
        'jetpack_related-posts', 'jetpack-slideshow', 'jetpack-subscriptions', 'jetpack-widgets',
        'gravatar-profile-widget', 'grunion.css', 'infinity-twentyten', 'infinity-twentyeleven',
        'infinity-twentytwelve', 'noticons', 'remove_meta_box', 'post-by-email', 'presentations', 'publicize',
        'sharedaddy', 'sharing', 'stats_reports_css', 'tiled-gallery', 'the-neverending-homepage',
        'widget-conditions', 'widget-grid-and-list',
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
    protected function init() : void
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

            if ('default' === $key && !OL_ZEUS_ISADMIN) {
                add_filter('jetpack_get_default_modules', '__return_empty_array');
                continue;
            }

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_filter('jetpack_implode_frontend_css', '__return_false');
                continue;
            }

            if ('remove_meta_box' === $key && OL_ZEUS_ISADMIN) {
                add_action('wp_dashboard_setup', function () {
                    remove_meta_box('jetpack_summary_widget', 'dashboard', 'normal');
                });

                continue;
            }

            if ('sharing' === $key) {
                add_action('loop_start', function () {
                    remove_filter('the_content', 'sharing_display', 19);
                    remove_filter('the_excerpt', 'sharing_display', 19);

                    if (class_exists('Jetpack_Likes')) {
                        remove_filter('the_content', [\Jetpack_Likes::init(), 'post_likes'], 30);
                    }
                });

                add_action('wp_footer', function () {
                    if (function_exists('sharing_display')) {
                        remove_action('wp_footer', 'sharing_add_footer');
                        add_action('wp_footer', 'sharing_add_footer', 100);
                    }
                }, 5);

                continue;
            }

            add_action('wp_print_styles', function () use ($key) {
                wp_deregister_style($key);
            });
        }
    }
}
