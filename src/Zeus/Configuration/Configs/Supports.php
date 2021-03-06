<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;

/**
 * Supports configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

class Supports extends Configuration
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

        // Iterate on configurations
        foreach ($this->configurations as $key => $props) {
            $props = !is_array($props) && !is_bool($props) ? [$props] : $props;

            if ('post_type' === $key) {
                // Add or Remove post type supports
                $this->addRemovePostTypeSupport($props);
            } else {
                // Add new theme supports
                $this->addRemoveThemeSupport($key, $props);
            }
        }
    }

    /**
     * Add or Remove post type supports to WP.
     *
     * @param  array   $props
     */
    protected function addRemovePostTypeSupport($props) : void
    {
        // Check props
        if (empty($props)) {
            return;
        }

        add_action('init', function () use ($props) {
            // Set available supports
            $available = ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats'];

            // Iterate on all
            foreach ($props as $posttype => $args) {
                if (!post_type_exists($posttype) || empty($args)) {
                    continue;
                }

                // Iterate on actions
                foreach ($args as $action => $supports) {
                    if (!in_array($action, ['add', 'remove']) || empty($supports)) {
                        continue;
                    }

                    // Extract only available supports
                    $supports = !is_array($supports) ? [$supports] : $supports;
                    $supps = array_intersect($supports, $available);

                    // Action on supports
                    if ('add' === $action) {
                        // Add supports
                        add_post_type_support($posttype, $supps);
                    } else {
                        // Remove supports one by one
                        foreach ($supps as $s) {
                            remove_post_type_support($posttype, $s);
                        }
                    }
                }
            }
        });
    }

    /**
     * Add theme supports to WP.
     *
     * @param  string  $key
     * @param  mixed   $props
     */
    protected function addRemoveThemeSupport($key, $props = []) : void
    {
        // Setup theme
        add_action('after_setup_theme', function () use ($key, $props) {
            // Check props
            if (is_bool($props) && !$props) {
                // Remove theme support
                remove_theme_support($key);
            } else if (empty($props)) {
                // Add theme support with default configuration
                add_theme_support($key);
            } else {
                // Add theme support with configurations
                add_theme_support($key, $props);
            }
        });
    }
}
