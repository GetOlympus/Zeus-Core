<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Sidebars configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

class Sidebars extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init() : void
    {
        // Check configurations
        if (empty($this->configurations)) {
            return;
        }

        // Add sidebars
        $this->addSidebars($this->configurations);
    }

    /**
     * Register sidebars to WP.
     *
     * @param  array   $configs
     */
    protected function addSidebars($configs) : void
    {
        // Define defaults
        $default = [
            'name'          => Translate::t('configuration.sidebars.labels.name'),
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
