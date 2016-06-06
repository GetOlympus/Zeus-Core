<?php

namespace GetOlympus\Hera\Configuration\Controller;

use GetOlympus\Hera\Configuration\Controller\Configuration;

/**
 * Hera Menus controller
 *
 * @package Olympus Hera
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

class Menus extends Configuration
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
        foreach ($configs as $key => $description) {
            $this->addMenu($key, $description);
        }
    }

    /**
     * Register sidebar to WP.
     *
     * @param string $key
     * @param string $description
     */
    public function addMenu($key, $description)
    {
        // Check description
        $description = empty($description) ? $key : $description;

        // Register menu navigation
        register_nav_menu($key, $description);
    }
}
