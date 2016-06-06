<?php

namespace GetOlympus\Hera\Configuration\Controller;

use GetOlympus\Hera\Configuration\Controller\Configuration;

/**
 * Hera Sidebars controller
 *
 * @package Olympus Hera
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

class Sidebars extends Configuration
{
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
        foreach ($configs as $key => $props) {
            $props = !is_array($props) ? [$props] : $props;
            $this->addSidebar($key, $props);
        }
    }

    /**
     * Register sidebar to WP.
     *
     * @param string $key
     * @param array  $props
     */
    public function addSidebar($key, $props)
    {
        // Check props
        if (empty($props)) {
            $props['id'] = $key;
        }

        // Set id
        $props['id'] = isset($props['id']) && !empty($props['id']) ? $props['id'] : $key;

        // Register sidebar
        register_sidebar($props);
    }
}
