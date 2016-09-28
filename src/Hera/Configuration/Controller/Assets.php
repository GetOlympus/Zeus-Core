<?php

namespace GetOlympus\Hera\Configuration\Controller;

use GetOlympus\Hera\Configuration\Controller\Configuration;

/**
 * Hera Assets controller
 *
 * @package Olympus Hera
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

class Assets extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init()
    {
        // Works only on frontend site
        if (OLH_ISADMIN) {
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
            $props = !is_array($props) && !is_bool($props) ? [$props] : $props;

            if ('scripts' === $key) {
                // Enqueue scripts
                $this->enqueueScripts($props);
            } else {
                // Enqueue styles
                $this->enqueueStyles($props);
            }
        }
    }

    /**
     * Enqueue scripts.
     *
     * @param array $scripts
     */
    public function enqueueScripts($scripts)
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
                if (!$opts) {
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
     * @param array $styles
     */
    public function enqueueStyles($styles)
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
