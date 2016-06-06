<?php

namespace GetOlympus\Hera\Configuration\Controller;

use GetOlympus\Hera\Configuration\Controller\Configuration;
use GetOlympus\Hera\Render\Controller\Render;

/**
 * Hera Settings controller
 *
 * @package Olympus Hera
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
        'admin-css',
        'admin-footer',
        'admin-menu-order',
        'admin-meta-boxes',
        'clean-headers',
        'comments-fields-order',
        'jpeg-quality',
        'login-shake',
        'login-style',
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

            $func = Render::toFunction($key).'Setting';
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
     * Remove some items from WP admin bar.
     *
     * @param boolean $css
     */
    public function adminCssSetting($css)
    {
        if (!$css) {
            return;
        }

        add_action('admin_enqueue_scripts', function (){
            wp_enqueue_style('olympus-core', OLH_URI.'css/olympus-core.css', false);
        }, 10);
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
            return !$menu_ord ? true : $args;
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
                if (!is_array($widget) || 3 !== count($widget)) {
                    continue;
                }

                $plugin = $widget[0];
                $page = $widget[1];
                $column = $widget[2];

                // Remove item
                remove_meta_box($plugin, $page, $column);
            }
        });
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
            } else {
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
     * Define wether if WP has to shake the login box or not.
     *
     * @param boolean $shake
     */
    public function loginShakeSetting($shake)
    {
        if ($shake) {
            return;
        }

        add_action('login_head', function (){
            remove_action('login_head', 'wp_shake_js', 12);
        });
    }

    /**
     * Define wether if WP login has to be redesigned or not.
     *
     * @param boolean $style
     */
    public function loginStyleSetting($style)
    {
        if (!$style) {
            return;
        }

        add_action('login_enqueue_scripts', function (){
            wp_enqueue_style('olympus-login', OLH_URI.'css/olympus-login.css', false);
        }, 10);

        add_action('login_enqueue_scripts', function (){
            //wp_enqueue_script('olympus-login', OLH_URI.'js/olympus-login.js', false);
        }, 1);

        add_filter('login_headerurl', function ($url) {
            return OLH_HOME;
        });
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
