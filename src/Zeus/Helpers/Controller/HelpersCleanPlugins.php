<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Helpers\Controller\HelpersClean;

/**
 * Clean Plugins helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class HelpersCleanPlugins extends HelpersClean
{
    /**
     * @var array
     */
    protected $available = [
        'bbpress'               => true,
        'contact-form'          => true,
        'google-tag-manager'    => true,
        'gravity-form'          => true,
        'jetpack'               => true,
        'the-events-calendar'   => true,
        'w3tc'                  => true,
        'woocommerce'           => true,
        'wp-rocket'             => true,
        'wp-socializer'         => true,
        'yarpp'                 => true,
        'yoast'                 => true,
    ];

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param  array   $args
     */
    public function init($args)
    {
        if (empty($args)) {
            return;
        }

        // Special case
        if (is_bool($args) && $args) {
            $args = $this->available;
        }

        // Iterate on all
        foreach ($args as $plugin => $settings) {
            $plugin = strtolower($plugin);

            if (!array_key_exists($plugin, $this->available)) {
                continue;
            }

            // Build class name
            $plugin = str_replace('_', '-', $plugin);
            $classname = Helpers::toFunctionFormat($plugin);
            $classname = 'GetOlympus\\Zeus\\Helpers\\Controller\\HelpersPlugin'.ucfirst($classname);

            // Instanciate object
            $class = new $classname($settings);
            $class->init();
        }
    }
}
