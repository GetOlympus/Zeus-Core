<?php

namespace GetOlympus\Hera\Configuration\Controller;

use GetOlympus\Hera\Configuration\Controller\Configuration;

/**
 * Hera Shortcodes controller
 *
 * @package Olympus Hera
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

class Shortcodes extends Configuration
{
    private $configs = [];

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

        // Update
        $this->configs = $configs;

        // Enable backend~frontend actions
        if (OLH_ISADMIN) {
            // Backend
            add_action('init', [$this, 'addShortcodesBackend']);
        } else {
            // Frontend
            add_action('init', [$this, 'addShortcodesFrontend']);
        }
    }

    /**
     * Enable shortcodes to tinyMCE WP.
     */
    public function addShortcodesBackend()
    {
        // Check user role
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // Use only if tinyMCE is enabled
        if ('true' == get_user_option('rich_editing')) {
            add_filter('mce_buttons', [$this, 'addButtons']);
            add_filter('mce_external_plugins', [$this, 'addPlugins']);
        }
    }

    /**
     * Add buttons to tinyMCE.
     *
     * @param array $buttons
     */
    public function addButtons($buttons)
    {
        // Add a first separator
        array_push($buttons, '|');

        // Iterate on configs
        foreach ($this->configs as $key => $file) {
            if (empty($file) && 'media' !== $key) {
                continue;
            }

            // Update buttons
            array_push($buttons, $key);
        }

        return $buttons;
    }

    /**
     * Add plugins to tinyMCE.
     *
     * @param array $plugins
     */
    public function addPlugins($plugins)
    {
        // Iterate on configs
        foreach ($this->configs as $key => $file) {
            $plugins[$key] = $file;
        }

        return $plugins;
    }

    /**
     * Enable shortcodes WP functions.
     */
    public function addShortcodesFrontend()
    {
        // Default content formatter
        remove_filter('the_content', 'wpautop');
        remove_filter('the_content', 'wptexturize');
        add_filter('the_content', [&$this, 'contentFormatter'], 99);

        // Iterate on configs
        foreach ($this->configs as $key => $file) {
            // Avoid special cases
            if (empty($file)) {
                continue;
            }

            // Add shortcode
            add_shortcode($key, function ($atts = null, $content = null) use ($key) {
                /**
                 * Hook to customize shortcode function.
                 *
                 * @var     string  $key
                 * @param   array   $atts
                 * @param   string  $content
                 * @return  string  $content
                 */
                return apply_filters('olh_shortcodes_'.$key, $atts, $content);
            });
        }
    }

    /**
     * Delete Wordpress auto-formatting.
     */
    public function contentFormatter($content)
    {
        $new_content = '';
        $pattern_full = '{(\[raw\].*?\[/raw\])}is';
        $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
        $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($pieces as $piece) {
            if (preg_match($pattern_contents, $piece, $matches)) {
                $new_content .= $matches[1];
            }
            else {
                $new_content .= wptexturize(wpautop($piece));
            }
        }

        return $new_content;
    }
}
