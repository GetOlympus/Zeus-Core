<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Helpers\Controller\HelpersCleanCore;
use GetOlympus\Zeus\Helpers\Controller\HelpersCleanFeatures;
use GetOlympus\Zeus\Helpers\Controller\HelpersCleanHeaders;
use GetOlympus\Zeus\Helpers\Controller\HelpersCleanPlugins;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Clean controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Clean extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init()
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
     * @param array $args
     */
    public function coreClean($args)
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new HelpersCleanCore)->init($args);
    }

    /**
     * Define what default WordPress feature to disable.
     *
     * @param array $args
     */
    public function featuresClean($args)
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new HelpersCleanFeatures)->init($args);
    }

    /**
     * Define what to clean from the theme header frontend, via the "remove_action" hook.
     *
     * @param array $args
     */
    public function headersClean($args)
    {
        if (empty($args) || OL_ZEUS_ISADMIN) {
            return;
        }

        // Initialization
        (new HelpersCleanHeaders)->init($args);
    }

    /**
     * Define what to clean from plugins, via the right hook.
     *
     * @param array $args
     */
    public function pluginsClean($args)
    {
        if (empty($args)) {
            return;
        }

        // Initialization
        (new HelpersCleanPlugins)->init($args);
    }
}
