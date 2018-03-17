<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Common\Controller\Common;
use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Settings controller
 *
 * @package Olympus Zeus-Core
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

class Settings extends Configuration
{
    /**
     * @var array
     */
    protected $available = [
        'admin-bar',
        'admin-footer',
        'admin-menu-order',
        'admin-meta-boxes',
        'clean-assets',
        'clean-headers',
        'comments-fields-order',
        'jpeg-quality',
        'login-shake',
        'login-style',
        'login-urls',
        'shutdown',
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

            $func = Common::toFunctionFormat($key).'Setting';
            $this->$func($args);
        }
    }

    /**
     * Remove some items from WP admin bar.
     *
     * @param array $args
     */
    public function adminBarSetting($args)
    {
        add_action('wp_before_admin_bar_render', function () use ($args){
            global $wp_admin_bar;

            // Iterate on all
            foreach ($args as $item) {
                $wp_admin_bar->remove_menu($item);
            }
        });
    }

    /**
     * Update WP footer copyright.
     *
     * @param string $description
     */
    public function adminFooterSetting($description)
    {
        // Work on description in case of an array
        $desc = is_array($description) ? $description[0] : $description;

        add_filter('admin_footer_text', function () use ($desc){
            echo '<span id="footer-thankyou">'.$desc.'</span>';
        });
    }

    /**
     * Reorder WP admin main menu.
     *
     * @param array $args
     */
    public function adminMenuOrderSetting($args)
    {
        add_filter('custom_menu_order', '__return_true');
        add_filter('menu_order', function ($menu_ord) use ($args){
            return !$menu_ord ? [] : $args;
        });
    }

    /**
     * Remove some admin widgets.
     *
     * @param array $args
     */
    public function adminMetaBoxesSetting($args)
    {
        add_action('wp_dashboard_setup', function () use ($args){
            // Iterate on all
            foreach ($args as $widget) {
                if (!is_array($widget)) {
                    continue;
                }

                $count = count($widget);

                if (3 > $count) {
                    continue;
                }

                // Remove item
                if (3 === $count) {
                    $plugin = $widget[0];
                    $page = $widget[1];
                    $column = $widget[2];

                    remove_meta_box($plugin, $page, $column);
                }
                // Add item
                else if (4 <= $count && 'add' === $widget[0]) {
                    $id = $widget[1];
                    $title = $widget[2];
                    $content = $widget[3];
                    $control = isset($widget[4]) ? $widget[4] : null;
                    $callback_args = isset($widget[5]) && is_array($widget[5]) ? $widget[5] : null;

                    wp_add_dashboard_widget($id, $title, function () use ($content){
                        echo $content;
                    }, $control, $callback_args);
                }
            }
        });
    }

    /**
     * Remove assets version.
     *
     * @param boolean $clean
     */
    public function cleanAssetsSetting($clean)
    {
        if (!$clean) {
            return;
        }

        // Remove WP Version from styles
        add_filter('style_loader_src', function ($src){
            return strpos($src, 'ver=') ? remove_query_arg('ver', $src) : $src;
        }, 9999);

        // Remove WP Version from scripts
        add_filter('script_loader_src', function ($src){
            return strpos($src, 'ver=') ? remove_query_arg('ver', $src) : $src;
        }, 9999);
    }

    /**
     * Define what to clean from the theme header frontend, via the "remove_action" hook.
     *
     * @param array $args
     */
    public function cleanHeadersSetting($args)
    {
        $available = [
            'adjacent_posts_rel_link_wp_head',
            'emoji',
            'feed_links',
            'feed_links_extra',
            'index_rel_link',
            'rsd_link',
            'wlwmanifest_link',
            'wp_admin_bar_init',
            'wp_dlmp_l10n_style',
            'wp_generator',
            'wp_shortlink_wp_head',
        ];

        // Iterate on all
        foreach ($args as $key) {
            if (!in_array($key, $available)) {
                continue;
            }

            if ('wp_admin_bar_init' === $key) {
                add_filter('show_admin_bar', '__return_false');
                remove_action('init', 'wp_admin_bar_init');
            }
            else if ('automatic-feed-links' === $key) {
                remove_theme_support($key);
            }
            else if ('emoji' === $key) {
                remove_action('wp_head', 'print_emoji_detection_script', 7);
                remove_action('wp_print_styles', 'print_emoji_styles');
                remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
                remove_filter('the_content_feed', 'wp_staticize_emoji');
                remove_filter('comment_text_rss', 'wp_staticize_emoji');
            }
            else {
                remove_action('wp_head', $key);
            }
        }
    }

    /**
     * Comment fields in wanted order.
     *
     * @param array $fields
     */
    public function commentsFieldsOrderSetting($fields)
    {
        add_filter('comment_form_fields', function ($comment_fields) use ($fields){
            $new_fields = [];

            // Iterate on fields
            foreach ($fields as $field) {
                if (!isset($comment_fields[$field])) {
                    continue;
                }

                $new_fields[$field] = $comment_fields[$field];
            }

            return $new_fields;
        });
    }

    /**
     * Update JPEG quality of generated images.
     *
     * @param integer $quality
     */
    public function jpegQualitySetting($quality)
    {
        // Work on quality
        $q = (integer) $quality;
        $q = 0 < $q && $q < 100 ? $q : 75;

        // Apply filter hook
        add_filter('jpeg_quality', create_function('', 'return '.$q.';'));
    }

    /**
     * Define wether if WP has to shut the DB connections off or not.
     *
     * @param array $args
     */
    public function shutdownSetting($args)
    {
        add_action('shutdown', function (){
            global $wpdb;
            unset($wpdb);
        }, 99);
    }
}
