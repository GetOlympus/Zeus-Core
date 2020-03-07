<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;

/**
 * Shortcodes configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

class Shortcodes extends Configuration
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * Add all usefull WP filters and hooks.
     */
    public function init() : void
    {
        // Check filepath
        if (empty($this->filepath)) {
            return;
        }

        // Get configurations
        $settings = include $this->filepath;

        // Check
        if (empty($settings)) {
            return;
        }

        // Update
        $this->configs = $settings;

        // Enable backend~frontend actions
        $append = OL_ZEUS_ISADMIN ? 'Backend' : 'Frontend';
        add_action('init', [$this, 'addShortcodes'.$append]);
    }

    /**
     * Enable shortcodes to tinyMCE WP.
     */
    public function addShortcodesBackend() : void
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
     * @param  array   $buttons
     *
     * @return array
     */
    public function addButtons($buttons) : array
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
     * @param  array   $plugins
     *
     * @return array
     */
    public function addPlugins($plugins) : array
    {
        // Iterate on configs
        foreach ($this->configs as $key => $file) {
            if (empty($file)) {
                continue;
            }

            $plugins[$key] = $file;
        }

        return $plugins;
    }

    /**
     * Enable shortcodes WP functions.
     */
    public function addShortcodesFrontend() : void
    {
        // Default content formatter
        remove_filter('the_content', 'wpautop');
        remove_filter('the_content', 'wptexturize');
        add_filter('the_content', [$this, 'contentFormatter'], 99);

        remove_filter('comment_text', 'wpautop');
        remove_filter('comment_text', 'wptexturize');
        add_filter('comment_text', [$this, 'contentFormatter'], 99);

        remove_filter('the_excerpt', 'wpautop');
        remove_filter('the_excerpt', 'wptexturize');
        add_filter('the_excerpt', [$this, 'contentFormatter'], 99);

        // Iterate on configs
        foreach ($this->configs as $key => $file) {
            // Avoid special cases
            if (false !== $file && empty($file)) {
                continue;
            }

            // Add shortcode
            add_shortcode($key, function ($atts = null, $content = null) use ($key) {
                /**
                 * Hook to customize shortcode function.
                 *
                 * @var    string  $key
                 * @param  array   $atts
                 * @param  string  $content
                 *
                 * @return string
                 */
                return apply_filters('ol_zeus_shortcodes_'.$key, $atts, $content);
            });
        }
    }

    /**
     * Delete Wordpress auto-formatting.
     *
     * @return string
     */
    public function contentFormatter($content) : string
    {
        $new_content = '';
        $pattern_full = '{(\[raw\].*?\[/raw\])}is';
        $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
        $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($pieces as $piece) {
            if (preg_match($pattern_contents, $piece, $matches)) {
                $new_content .= $matches[1];
            } else {
                $new_content .= wpautop($piece);
            }
        }

        return $new_content;
    }
}
