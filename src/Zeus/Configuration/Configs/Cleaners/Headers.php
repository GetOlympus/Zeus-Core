<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners;

use GetOlympus\Zeus\Configuration\Configs\Cleaners\Cleaner;

/**
 * Headers cleaner
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Headers extends Cleaner
{
    /**
     * @var array
     */
    protected $available = [
        'adjacent_posts_rel'       => true, // Remove the next and previous post links from the header
        'defer_javascripts'        => true, // Defer Javascripts calls
        'feed_links'               => true, // Remove Automatics RSS links, which will still work with your own links
        'shortlink'                => true, // Remove the shortlink url from header
        'recent_comments'          => true, // Remove a block of inline CSS used by old themes from the header
        'wp_generator'             => true, // Remove WP meta generator tag

        'index_rel_link'           => true, // Remove rel navigation links
        'parent_post_rel_link'     => true, // Remove rel parent link
        'rest_output_link_wp_head' => true, // Remove REST API link tag
        'rsd_link'                 => true, // Remove Really Simple Discovery (RSD) links used for automatic pingbacks
        'start_post_rel_link'      => true, // Remove rel first post link
        'wp_dlmp_l10n_style'       => true, // Remove i18n styles
        'wp_resource_hints'        => true, // Remove dns-prefetch links from the header
        'wlwmanifest_link'         => true, // Remove the link to wlwmanifest.xml needed to support Windows Live Writer
    ];

    /**
     * Remove the next and previous post links from the header
     */
    protected function headersAdjacentPostsRel() : void
    {
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    }

    /**
     * Defer Javascripts calls
     */
    protected function headersDeferJavascripts() : void
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
    protected function headersFeedLinks() : void
    {
        remove_theme_support('automatic-feed-links');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    protected function headersIndexRelLink() : void
    {
        remove_action('wp_head', 'index_rel_link');
    }

    protected function headersParentPostRelLink() : void
    {
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    }

    /**
     * Remove a block of inline CSS used by old themes from the header
     */
    protected function headersRecentComments() : void
    {
        add_filter('show_recent_comments_widget_style', '__return_false');
    }

    protected function headersRestOutputLinkWpHead() : void
    {
        remove_action('wp_head', 'rest_output_link_wp_head');
    }

    protected function headersRsdLink() : void
    {
        remove_action('wp_head', 'rsd_link');
    }

    /**
     * Remove the shortlink url from header
     */
    protected function headersShortlink() : void
    {
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('template_redirect', 'wp_shortlink_header', 11, 0);
    }

    protected function headersStartPostRelLink() : void
    {
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
    }

    protected function headersWlwmanifestLink() : void
    {
        remove_action('wp_head', 'wlwmanifest_link');
    }

    /**
     * Remove WordPress meta generator tag
     */
    protected function headersWpGenerator() : void
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', function ($src) {
            return '';
        });
    }

    protected function headersWpDlmpL10nStyle() : void
    {
        remove_action('wp_head', 'wp_dlmp_l10n_style');
    }

    protected function headersWpResourceHints() : void
    {
        remove_action('wp_head', 'wp_resource_hints', 2);
    }
}
