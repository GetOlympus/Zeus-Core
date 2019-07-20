<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Helpers\Controller\HelpersClean;

/**
 * Clean Headers helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class HelpersCleanHeaders extends HelpersClean
{
    /**
     * @var array
     */
    protected $available = [
        'adjacent_posts_rel', // Remove the next and previous post links from the header
        'defer_javascripts',  // Defer Javascripts calls
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

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param  array   $args
     */
    public function init($args)
    {
        if (empty($args) || OL_ZEUS_ISADMIN) {
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

            if (in_array($key, ['index_rel_link', 'parent_post_rel_link', 'rest_output_link_wp_head', 'rsd_link', 'start_post_rel_link', 'wp_dlmp_l10n_style', 'wp_resource_hints', 'wlwmanifest_link'])) {
                $this->headerWpHead($key);
                continue;
            }

            $key = str_replace('_', '-', $key);

            $function = Helpers::toFunctionFormat($key);
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
     * Defer Javascripts calls
     */
    public function headerDeferJavascripts()
    {
        add_filter('script_loader_tag', function ($tag, $handle) {
            // Main WP jQuery is not concerned
            if (strpos($tag, '/wp-includes/js/jquery/jquery')) {
                return $tag;
            }

            // Defer on MSIE is not permitted
            if (isset($_SERVER['HTTP_USER_AGENT']) && false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.')) {
                return $tag;
            }

            return str_replace(' src', ' defer src', $tag);
        }, 10, 2);
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
     * @param  string  $key
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
}
