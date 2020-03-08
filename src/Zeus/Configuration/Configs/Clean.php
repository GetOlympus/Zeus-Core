<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;
use GetOlympus\Zeus\Configuration\Configs\Cleaners\Core as CleanerCore;
use GetOlympus\Zeus\Configuration\Configs\Cleaners\Features as CleanerFeatures;
use GetOlympus\Zeus\Configuration\Configs\Cleaners\Headers as CleanerHeaders;
use GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins as CleanerPlugins;

/**
 * Clean configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Clean extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init() : void
    {
        // Initialize filepath with configs
        $funcs = $this->getFunctions('Clean', [
            'core',
            'features',
            'headers',
            'plugins',
        ]);

        // Check functions
        if (empty($funcs)) {
            return;
        }

        // Iterate on functions
        foreach ($funcs as $key => $args) {
            $this->$key($args);
        }
    }

    /**
     * Define what default WordPress core feature to disable.
     *
     * @param  array   $args
     */
    protected function coreClean($args) : void
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new CleanerCore)->init($args);
    }

    /**
     * Define what default WordPress feature to disable.
     *
     * @param  array   $args
     */
    protected function featuresClean($args) : void
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new CleanerFeatures)->init($args);
    }

    /**
     * Define what to clean from the theme header frontend, via the "remove_action" hook.
     *
     * @param  array   $args
     */
    protected function headersClean($args) : void
    {
        if (empty($args) || OL_ZEUS_ISADMIN) {
            return;
        }

        // Initialization
        (new CleanerHeaders)->init($args);
    }

    /**
     * Define what to clean from plugins, via the right hook.
     *
     * @param  array   $args
     */
    protected function pluginsClean($args) : void
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new CleanerPlugins)->init($args);
    }
}
