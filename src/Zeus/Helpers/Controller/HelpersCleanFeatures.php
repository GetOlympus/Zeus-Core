<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Helpers\Controller\HelpersClean;

/**
 * Clean Features helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class HelpersCleanFeatures extends HelpersClean
{
    /**
     * @var array
     */
    protected $available = [
        'admin_bar',          // Remove admin bar on frontend pages
        'body_class',         // Remove unecessary details in `body_class()` method
        'capital_p_dangit',   // Remove the filter that converts "Wordpress" to "WordPress"
        'comment_autolinks',  // Remove auto-converted URLs in comments to avoid spammers
        'comments_reply',     // Loads the comment-reply JS file only when needed
        'dashicons',          // Remove Dashicons from admin bar for non-logged users
        'embeds',             // Remove embeds integration
        'embeds_script',      // Remove embeds script integration
        'emojicons',          // Remove emojicons integration
        'gravatar_queries',   // Remove Gravatar query strings
        'medium_large_size',  // Prevents WordPress from generating the medium_large 768px thumbnail size of image uploads
        'pdf_thumbnails',     // Remove PDF thumbnails generator
        'self_pingback',      // Remove self pingbacks
        'slow_heartbeat',     // Changes Heartbeat post calls from 15 to 60 seconds for less CPU usage
        'version',            // Remove WP Version (?ver=) from scripts and styles
    ];

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param  array   $args
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
        foreach ($args as $key) {
            $key = strtolower($key);

            if (!in_array($key, $this->available)) {
                continue;
            }

            $key = str_replace('_', '-', $key);

            $function = Helpers::toFunctionFormat($key);
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
     * Remove unecessary details in `body_class()` method
     */
    public function featureBodyClass()
    {
        add_filter('body_class', function ($classes, $class) {
            // Author pages
            if (isset($classes['author'])) {
                // Get author
                if (!$name = get_query_var('author_name')) {
                    $author = get_userdata(get_query_var('author'));
                } else {
                    $author = get_user_by('slug', $name);
                }

                // Get data to remove
                $removed[] = 'author-'.$author->ID;
                $removed[] = 'author-'.$author->user_nicename;

                // Remove classes
                $classes = array_diff($classes, $removed);
            }

            // Single pages
            if (isset($classes['single'])) {
                // Get post details
                $post = get_queried_object();
                $pid = get_queried_object_id();

                // Check post type
                if (isset($post->post_type)) {
                    $removed[] = 'single-'.sanitize_html_class($post->post_type, $pid);
                    $removed[] = 'postid-'.$pid;

                    // Remove classes
                    $classes = array_diff($classes, $removed);
                }
            }

            // Archive pages
            if (isset($classes['category']) || isset($classes['tag']) || isset($classes['tax'])) {
                // Get object details
                $obj = get_queried_object();

                // Check object ID
                if (isset($obj->term_id)) {
                    // Get class
                    $obj_class = sanitize_html_class($obj->slug, $obj->term_id);
                    $obj_class = is_numeric($obj_class) || !trim($obj_class, '-') ? $obj->term_id : $obj_class;

                    // Remove details
                    if (isset($classes['category'])) {
                        $removed[] = 'category-'.$obj_class;
                        $removed[] = 'category-'.$obj->term_id;
                    } else if (isset($classes['tag'])) {
                        $removed[] = 'tag-'.$obj_class;
                        $removed[] = 'tag-'.$obj->term_id;
                    } else if (isset($classes['tax'])) {
                        $removed[] = 'tax-'.sanitize_html_class($obj->taxonomy);
                        $removed[] = 'term-'.$obj_class;
                        $removed[] = 'term-'.$obj->term_id;
                    }

                    // Remove classes
                    $classes = array_diff($classes, $removed);
                }
            }

            // Return all classes
            return array_merge($classes, (array)$class);
        }, 10, 2);
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
        add_action('wp_print_scripts', function () {
            if (is_singular() && (1 === get_option('thread_comments')) && comments_open() && have_comments()) {
                wp_enqueue_script('comment-reply');
            } else {
                wp_dequeue_script('comment-reply');
            }
        }, 100);
    }

    /**
     * Remove Dashicons from admin bar for non-logged users
     */
    public function featureDashicons()
    {
        add_action('wp_print_styles', function () {
            if (!is_admin_bar_showing() && !is_customize_preview()) {
                wp_deregister_style('dashicons');
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

        add_filter('tiny_mce_plugins', function ($plugins) {
            return array_diff($plugins, ['wpembed']);
        });

        add_filter('rewrite_rules_array', function ($rules) {
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
     * Remove embeds script integration
     */
    public function featureEmbedsScript()
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        add_action('wp_footer', function () {
            wp_deregister_script('wp-embed');
        });
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

        add_filter('tiny_mce_plugins', function ($plugins) {
            return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
        });

        add_filter('wp_resource_hints', function ($urls, $relation_type) {
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
     * Remove Gravatar query strings
     */
    public function featureGravatarQueries()
    {
        add_filter('get_avatar_url', function ($url) {
            $url_parts = explode('?', $url);
            return $url_parts[0];
        });
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
        add_filter('fallback_intermediate_image_sizes', function () {
            return [];
        });
    }

    /**
     * Remove self pingbacks
     */
    public function featureSelfPingback()
    {
        add_action('pre_ping', function (&$links) {
            $home = get_option('home');

            foreach ($links as $l => $link) {
                if (0 === strpos($link, $home)) {
                    unset($links[$l]);
                }
            }
        });
    }

    /**
     * Changes Heartbeat post calls from 15 to 60 seconds for less CPU usage
     */
    public function featureSlowHeartbeat()
    {
        add_filter('heartbeat_settings', function ($settings) {
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
}
