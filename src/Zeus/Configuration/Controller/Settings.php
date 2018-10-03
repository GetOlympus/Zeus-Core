<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Settings controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
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
        'comments-fields-order',
        'jpeg-quality',
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

            $func = Helpers::toFunctionFormat($key).'Setting';
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
        if (empty($args)) {
            return;
        }

        add_action('wp_before_admin_bar_render', function () use ($args) {
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

        add_filter('admin_footer_text', function () use ($desc) {
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
        if (empty($args)) {
            return;
        }

        add_filter('custom_menu_order', '__return_true');
        add_filter('menu_order', function ($menu_ord) use ($args) {
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
        if (empty($args)) {
            return;
        }

        add_action('wp_dashboard_setup', function () use ($args) {
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
                } else if (4 <= $count && 'add' === $widget[0]) {
                    // Add item
                    $id = $widget[1];
                    $title = $widget[2];
                    $content = $widget[3];
                    $control = isset($widget[4]) ? $widget[4] : null;
                    $callback_args = isset($widget[5]) && is_array($widget[5]) ? $widget[5] : null;

                    wp_add_dashboard_widget($id, $title, function () use ($content) {
                        echo $content;
                    }, $control, $callback_args);
                }
            }
        });
    }

    /**
     * Comment fields in wanted order.
     *
     * @param array $fields
     */
    public function commentsFieldsOrderSetting($fields)
    {
        add_filter('comment_form_fields', function ($comment_fields) use ($fields) {
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
        $q = is_bool($quality) && $quality ? 72 : (integer) $quality;
        $q = 0 < $q && $q <= 100 ? $q : 72;

        // Apply filter hook
        add_filter('jpeg_quality', function () use ($q) {
            return $q;
        });
    }
}
