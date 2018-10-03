<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Clean controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Clean extends Configuration
{
    /**
     * @var array
     */
    protected $available = [
        'core',
        'features',
        'headers',
        'plugins',
    ];

    /**
     * Add all usefull WP filters and hooks.
     */
    public function init()
    {
        // Check filepath
        if (empty($this->filepath)) {
            return;
        }

        // Get configurations
        $configs = include $this->filepath;

        // Check
        if (empty($configs)) {
            return;
        }

        // Iterate on configs
        foreach ($configs as $key => $args) {
            if (!in_array($key, $this->available) || empty($args)) {
                continue;
            }

            $func = Helpers::toFunctionFormat($key).'Clean';
            $this->$func($args);
        }
    }

    /**
     * Define what default WordPress core feature to disable.
     *
     * @param array $args
     */
    public function coreClean($args)
    {
        if (empty($args)) {
            return;
        }

        $available = [
            'jquery-migrate',     // Remove jQuery Migrate default script
            'json-api',           // Remove json api and link from header
            'oembed-scripts',     // Remove default oEmbed scripts
            'post-custom-metabox',// Remove post custom metaboxes from post editor to prevent very slow queries
            'shutdown',           // Define wether if WP has to shut the DB connections off or not
            'xmlrpc',             // Remove XMLRPC
        ];

        // Special case
        if (is_bool($args) && $args) {
            $args = $available;
        }

        // Iterate on all
        foreach ($args as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            $function = Helpers::toFunctionFormat($key);
            $function = 'core'.ucfirst($function);
            $this->$function();
        }
    }

    /**
     * Remove jQuery Migrate default script
     */
    public function coreJqueryMigrate()
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        global $wp_scripts;

        if (!empty($wp_scripts->registered['jquery'])) {
            $wp_scripts->registered['jquery']->deps = array_diff($wp_scripts->registered['jquery']->deps, ['jquery-migrate']);
        }
    }

    /**
     * Remove json api and link from header
     */
    public function coreJsonApi()
    {
        if (!OL_ZEUS_ISADMIN) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }

        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);

        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');

        // add_filter('rest_enabled', '__return_false'); // Deprecated
        add_filter('rest_jsonp_enabled', '__return_false');
        add_filter('rest_authentication_errors', function ($access){
            return new WP_Error(
                'rest_disabled',
                __('The REST API on this site has been disabled.'),
                [
                    'status' => rest_authorization_required_code(),
                ]
            );
        });
    }

    /**
     * Remove default oEmbed scripts
     */
    public function coreOembedScripts()
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        wp_deregister_script('wp-embed');
    }

    /**
     * Remove post custom metaboxes from post editor to prevent very slow queries
     */
    public function corePostCustomMetabox()
    {
        $pts = get_post_types('', 'names');

        foreach ($pts as $post_type) {
            remove_meta_box('postcustom', $post_type, 'normal');
        }
    }

    /**
     * Define wether if WP has to shut the DB connections off or not
     */
    public function coreShutdown()
    {
        add_action('shutdown', function () {
            global $wpdb;
            unset($wpdb);
        }, 99);
    }

    /**
     * Remove XMLRPC
     */
    public function coreXmlrpc()
    {
        add_filter('xmlrpc_enabled', '__return_false');

        add_filter('wp_headers', function ($headers){
            unset($headers['X-Pingback']);
            return $headers;
        });

        remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
        add_filter('xmlrpc_enabled', '__return_false');

        add_filter('xmlrpc_methods', function ($methods){
            unset($methods['pingback.ping']);
            return $methods;
        });
    }

    /**
     * Define what default WordPress feature to disable.
     *
     * @param array $args
     */
    public function featuresClean($args)
    {
        if (empty($args)) {
            return;
        }

        $available = [
            'admin_bar',          // Remove admin bar on frontend pages
            'capital_p_dangit',   // Remove the filter that converts "Wordpress" to "WordPress"
            'comment_autolinks',  // Remove auto-converted URLs in comments to avoid spammers
            'comments_reply',     // Loads the comment-reply JS file only when needed
            'embeds',             // Remove embeds integration
            'emojicons',          // Remove emojicons integration
            'medium_large_size',  // Prevents WordPress from generating the medium_large 768px thumbnail size of image uploads
            'pdf_thumbnails',     // Remove PDF thumbnails generator
            'slow_heartbeat',     // Changes Heartbeat post calls from 15 to 60 seconds for less CPU usage
            'version',            // Remove WP Version (?ver=) from scripts and styles
        ];

        // Special case
        if (is_bool($args) && $args) {
            $args = $available;
        }

        // Iterate on all
        foreach ($args as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            $function = Helpers::toFunctionFormat($key, '_');
            $function = 'feature'.ucfirst($function);
            $this->$function();
        }
    }

    /**
     * Remove admin bar on frontend pages
     */
    public function featureAdminBar()
    {
        add_filter('show_admin_bar', '__return_false');
        remove_action('init', 'wp_admin_bar_init');
    }

    /**
     * Remove the filter that converts "Wordpress" to "WordPress"
     */
    public function featureCapitalPDangit()
    {
        foreach (['comment_text', 'the_content', 'the_title', 'wp_title'] as $filter) {
            $priority = has_filter($filter, 'capital_P_dangit');

            if (false !== $priority) {
                remove_filter($filter, 'capital_P_dangit', $priority);
            }
        }
    }

    /**
     * Remove auto-converted URLs in comments to avoid spammers
     */
    public function featureCommentAutolinks()
    {
        // Simple remove filter hook
        remove_filter('comment_text', 'make_clickable', 9);
    }

    /**
     * Loads the comment-reply JS file only when needed
     */
    public function featureCommentsReply()
    {
        add_action('wp_print_scripts', function (){
            if (is_singular() && (1 === get_option('thread_comments')) && comments_open() && have_comments()) {
                wp_enqueue_script('comment-reply');
            } else {
                wp_dequeue_script('comment-reply');
            }
        }, 100);
    }

    /**
     * Remove embeds integration
     */
    public function featureEmbeds()
    {
        // Remove the embed query var
        global $wp;
        $wp->public_query_vars = array_diff($wp->public_query_vars, ['embed']);

        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');

        add_filter('tiny_mce_plugins', function ($plugins){
            return array_diff($plugins, ['wpembed']);
        });

        add_filter('rewrite_rules_array', function ($rules){
            foreach ($rules as $rule => $rewrite) {
                if (false !== strpos($rewrite, 'embed=true')) {
                    unset($rules[$rule]);
                }
            }

            return $rules;
        });

        remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
    }

    /**
     * Remove emojicons integration
     */
    public function featureEmojicons()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        add_filter('tiny_mce_plugins', function ($plugins){
            return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
        });

        add_filter('wp_resource_hints', function ($urls, $relation_type){
            if ('dns-prefetch' === $relation_type) {
                $emoji_url = 'https://s.w.org/images/core/emoji/';

                foreach ($urls as $key => $url) {
                    if (false !== strpos($url, $emoji_url)) {
                        unset($urls[$key]);
                    }
                }
            }

            return $urls;
        }, 10, 2);
    }

    /**
     * Prevents WordPress from generating the medium_large 768px thumbnail size of image uploads
     */
    public function featureMediumLargeSize()
    {
        // Simple add image size hook
        add_image_size('medium_large', 0, 0);
    }

    /**
     * Remove PDF thumbnails generator
     */
    public function featurePdfThumbnails()
    {
        add_filter('fallback_intermediate_image_sizes', function (){
            return array();
        });
    }

    /**
     * Changes Heartbeat post calls from 15 to 60 seconds for less CPU usage
     */
    public function featureSlowHeartbeat()
    {
        add_filter('heartbeat_settings', function ($settings){
            $settings['interval'] = 60;
            return $settings;
        });
    }

    /**
     * Remove WP Version (?ver=) from scripts and styles
     */
    public function featureVersion()
    {
        add_filter('style_loader_src', function ($src) {
            return strpos($src, 'ver=') ? remove_query_arg('ver', $src) : $src;
        }, 9999);

        add_filter('script_loader_src', function ($src) {
            return strpos($src, 'ver=') ? remove_query_arg('ver', $src) : $src;
        }, 9999);
    }

    /**
     * Define what to clean from the theme header frontend, via the "remove_action" hook.
     *
     * @param array $args
     */
    public function headersClean($args)
    {
        if (empty($args) || OL_ZEUS_ISADMIN) {
            return;
        }

        $available = [
            'adjacent_posts_rel', // Remove the next and previous post links from the header
            'feed_links',         // Remove Automatics RSS links, which will still work with your own links
            'shortlink',          // Remove the shortlink url from header
            'recent_comments',    // Remove a block of inline CSS used by old themes from the header
            'wp_generator',       // Remove WordPress meta generator tag

            'index_rel_link',     // Remove rel navigation links
            'parent_post_rel_link',     // Remove rel parent link
            'rest_output_link_wp_head', // Remove REST API link tag
            'rsd_link',           // Remove Really Simple Discovery (RSD) links used for automatic pingbacks
            'start_post_rel_link',      // Remove rel first post link
            'wp_dlmp_l10n_style', // Remove i18n styles
            'wp_resource_hints',  // Remove dns-prefetch links from the header
            'wlwmanifest_link',   // Remove the link to wlwmanifest.xml needed to support Windows Live Writer
        ];

        // Special case
        if (is_bool($args) && $args) {
            $args = $available;
        }

        // Iterate on all
        foreach ($args as $key) {
            $key = strtolower($key);

            if (!in_array($key, $available)) {
                continue;
            }

            if (in_array($key, ['index_rel_link', 'parent_post_rel_link', 'rest_output_link_wp_head', 'rsd_link', 'start_post_rel_link', 'wp_dlmp_l10n_style', 'wp_resource_hints', 'wlwmanifest_link'])) {
                $this->headerWpHead($key);
                continue;
            }

            $function = Helpers::toFunctionFormat($key, '_');
            $function = 'header'.ucfirst($function);
            $this->$function();
        }
    }

    /**
     * Remove the next and previous post links from the header
     */
    public function headerAdjacentPostsRel()
    {
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    }

    /**
     * Remove Automatics RSS links, which will still work with your own links
     */
    public function headerFeedLinks()
    {
        remove_theme_support('automatic-feed-links');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    /**
     * Remove the shortlink url from header
     */
    public function headerShortlink()
    {
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('template_redirect', 'wp_shortlink_header', 11, 0);
    }

    /**
     * Remove a block of inline CSS used by old themes from the header
     */
    public function headerRecentComments()
    {
        // Simple add filter hook
        add_filter('show_recent_comments_widget_style', '__return_false');
    }

    /**
     * Remove WordPress meta generator tag
     */
    public function headerWpGenerator()
    {
        remove_action('wp_head', 'wp_generator');

        add_filter('the_generator', function ($src) {
            return '';
        });
    }

    /**
     * Remove the next and previous post links from the header
     *
     * @param string $key
     */
    public function headerWpHead($key)
    {
        if (in_array($key, ['parent_post_rel_link', 'start_post_rel_link'])) {
            remove_action('wp_head', $key, 10, 0);
        } else if ('wp_resource_hints' === $key) {
            remove_action('wp_head', $key, 2);
        } else {
            remove_action('wp_head', $key);
        }
    }

    /**
     * Define what to clean from plugins, via the right hook.
     *
     * @param array $args
     */
    public function pluginsClean($args)
    {
        if (empty($args)) {
            return;
        }

        $available = [
            'contact-form',
            'jetpack',
            'w3tc',
            'woocommerce',
        ];

        // Special case
        if (is_bool($args) && $args) {
            $args = $available;
        }

        // Iterate on all
        foreach ($args as $plugin => $settings) {
            if (!in_array($plugin, $available)) {
                continue;
            }

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

        foreach ($settings as $key) {
            if (!in_array($key, $available)) {
                continue;
            }

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_action('wp_enqueue_scripts', function (){
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
            'enqueue_styles', 'AtD_style', 'jetpack_likes', 'jetpack_related-posts', 'jetpack-carousel', 'grunion.css',
            'the-neverending-homepage', 'infinity-twentyten', 'infinity-twentyeleven', 'infinity-twentytwelve',
            'noticons', 'post-by-email', 'publicize', 'sharedaddy', 'sharing', 'stats_reports_css', 'jetpack-widgets',
            'jetpack-slideshow', 'presentations', 'jetpack-subscriptions', 'tiled-gallery', 'widget-conditions',
            'jetpack_display_posts_widget', 'gravatar-profile-widget', 'widget-grid-and-list', 'jetpack-widgets',
        ];

        foreach ($settings as $key) {
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
            'can_print_comment',
        ];

        foreach ($settings as $key) {
            if (!in_array($key, $available)) {
                continue;
            }

            add_filter('w3tc_'.$key, '__return_false', 10, 1);
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
            'enqueue_styles', 'enqueue_styles_wordpress_only', 'wc_generator_tag',
        ];

        foreach ($settings as $key) {
            if (!in_array($key, $available)) {
                continue;
            }

            if ('enqueue_styles' === $key) {
                add_filter('woocommerce_enqueue_styles', function ($return_false){
                    return false;
                });
            } else if ('enqueue_styles_wordpress_only' === $key) {
                add_filter('woocommerce_enqueue_styles', function ($return_false){
                    return !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page() ? false : $return_false;
                });
            } else {
                add_action('get_header', function (){
                    remove_action('wp_head', 'wc_generator_tag');

                    if (isset($GLOBALS['woocommerce']) && is_object($GLOBALS['woocommerce'])) {
                        remove_action('wp_head', [$GLOBALS['woocommerce'], 'generator']);
                    }
                });

                add_action('woocommerce_init', function (){
                    remove_action('wp_head', 'wc_generator_tag');

                    if (isset($GLOBALS['woocommerce']) && is_object($GLOBALS['woocommerce'])) {
                        remove_action('wp_head', [$GLOBALS['woocommerce'], 'generator']);
                    }
                });
            }
        }
    }
}
