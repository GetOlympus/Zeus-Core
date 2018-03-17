<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Sidebars controller
 *
 * @package Olympus Zeus-Core
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

        // Add sidebars
        $this->addSidebars($configs);
    }

    /**
     * Register sidebars to WP.
     *
     * @param array $configs
     */
    public function addSidebars($configs)
    {
        // Define defaults
        $default = [
            'name'          => Translate::t('configuration.sidebar.name'),
            'id'            => '',
            'description'   => '',
            'class'         => '',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => '',
        ];

        // Check
        if (empty($configs)) {
            return;
        }

        // Iterate on configs
        foreach ($configs as $key => $props) {
            $props = !is_array($props) ? [$props] : $props;

            // Set id
            $props['id'] = isset($props['id']) && !empty($props['id']) ? $props['id'] : $key;

            // Set props
            $props = array_merge($default, $props);

            // Register sidebar
            register_sidebar($props);
        }
    }
}
