<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;

/**
 * Admin Themes configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

class AdminThemes extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init() : void
    {
        // Works only on backend site
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

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
        foreach ($configs as $key => $props) {
            $props = !is_array($props) ? [$props] : $props;

            // Add admin theme
            $this->addAdminTheme($key, $props);
        }
    }

    /**
     * Add admin theme.
     *
     * @param  string  $key
     * @param  array   $args
     */
    protected function addAdminTheme($key, $args) : void
    {
        // Check args
        if (empty($args)) {
            return;
        }

        $defaults = [
            'name'      => $key,
            'url'       => $key,
            'colors'    => [],
            'icons'     => []
        ];

        // Create options with args
        $opts = array_merge($defaults, $args);

        // Check options
        if (!isset($opts['name'], $opts['url'], $opts['colors'])) {
            return;
        }

        // Works on colors
        if (3 > count($opts['colors'])) {
            $opts['colors'][1] = isset($opts['colors'][1]) ? $opts['colors'][1] : $opts['colors'][0];
            $opts['colors'][2] = isset($opts['colors'][2]) ? $opts['colors'][2] : $opts['colors'][1];
        }

        // Works on icons
        if (isset($opts['icons'])) {
            $opts['icons'] = [
                'base'      => isset($opts['icons']['base']) ? $opts['icons']['base'] : $opts['colors'][0],
                'focus'     => isset($opts['icons']['focus']) ? $opts['icons']['focus'] : $opts['colors'][1],
                'current'   => isset($opts['icons']['current']) ? $opts['icons']['current'] : $opts['colors'][2],
            ];
        } else {
            $opts['icons'] = [
                'base'      => $opts['colors'][0],
                'focus'     => $opts['colors'][1],
                'current'   => $opts['colors'][2],
            ];
        }

        // Add custom admin theme
        wp_admin_css_color($key, $opts['name'], $opts['url'], $opts['colors'], $opts['icons']);
    }
}
