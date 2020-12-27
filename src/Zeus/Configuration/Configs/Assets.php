<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;

/**
 * Assets configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

class Assets extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init() : void
    {
        // Works only on frontend site
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        // Check configurations
        if (empty($this->configurations)) {
            return;
        }

        // Check current page
        if (in_array($GLOBALS['pagenow'], ['wp-login.php'])) {
            return;
        }

        $configs = $this->configurations;

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', function () use ($configs) {
            // Iterate on configs
            foreach ($configs as $key => $props) {
                $props = !is_array($props) && !is_bool($props) ? [$props] : $props;

                if ('scripts' === $key) {
                    // Enqueue scripts
                    $this->enqueueScripts($props);
                } else if ('styles' === $key) {
                    // Enqueue styles
                    $this->enqueueStyles($props);
                }
            }
        });
    }

    /**
     * Enqueue scripts.
     *
     * @param  array   $scripts
     */
    protected function enqueueScripts($scripts) : void
    {
        // Check scripts
        if (empty($scripts)) {
            return;
        }

        $defaults = [
            'src'       => false,
            'deps'      => [],
            'ver'       => false,
            'in_footer' => false
        ];

        // Iterate on all
        foreach ($scripts as $handle => $opts) {
            // Special case: de/register handle
            if (!is_array($opts)) {
                // Special case: jQuery-Migrate deregistration
                if ('jquery-migrate' === $handle && !$opts) {
                    add_action('wp_default_scripts', function ($scripts) {
                        if (OL_ZEUS_ISADMIN || empty($scripts->registered['jquery'])) {
                            return;
                        }

                        $jquery_dependencies = $scripts->registered['jquery']->deps;
                        $scripts->registered['jquery']->deps = array_diff($jquery_dependencies, array('jquery-migrate'));
                    });
                } else if (!$opts) {
                    wp_deregister_script($handle);
                } else {
                    wp_enqueue_script($handle);
                }

                continue;
            }

            // Merge options with defaults and enqueue script
            $opts = array_merge($defaults, $opts);
            wp_enqueue_script($handle, $opts['src'], $opts['deps'], $opts['ver'], $opts['in_footer']);
        }
    }

    /**
     * Enqueue styles.
     *
     * @param  array   $styles
     */
    protected function enqueueStyles($styles) : void
    {
        // Check styles
        if (empty($styles)) {
            return;
        }

        $defaults = [
            'src'   => false,
            'deps'  => [],
            'ver'   => false,
            'media' => 'all'
        ];

        // Iterate on all
        foreach ($styles as $handle => $opts) {
            if (!is_array($opts)) {
                continue;
            }

            // Merge options with defaults and enqueue style
            $opts = array_merge($defaults, $opts);
            wp_enqueue_style($handle, $opts['src'], $opts['deps'], $opts['ver'], $opts['media']);
        }
    }
}
