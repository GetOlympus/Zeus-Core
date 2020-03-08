<?php

namespace GetOlympus\Zeus\Configuration\Configs;

use GetOlympus\Zeus\Configuration\Configuration;

/**
 * Sizes configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

class Sizes extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     *
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/add_image_size
     */
    public function init() : void
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
            if (4 !== count($props)) {
                continue;
            }

            // Extract all vars
            list($width, $height, $crop, $label) = $props;

            // Add image sizes
            add_image_size($key, $width, $height, $crop);
        }

        // Add image names to droplist
        $this->addImagesDroplist($configs);
    }

    /**
     * Add images names to droplist.
     *
     * @param  array   $configs
     *
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/image_size_names_choose
     */
    protected function addImagesDroplist($configs) : void
    {
        // Add image names
        add_filter('image_size_names_choose', function ($sizes) use ($configs) {
            // New sizes array
            $new_sizes = [];

            // Iterate on sizes like:
            // @key => [@width, @height, @crop, @dropdown],
            foreach ($configs as $key => $props) {
                if (4 !== count($props)) {
                    continue;
                }

                // Extract last var
                $label = array_pop($props);

                // Check label
                if (empty($label)) {
                    continue;
                }

                // Add image sizes
                $new_sizes[$key] = $label;
            }

            return array_merge($sizes, $new_sizes);
        });
    }
}
