<?php

namespace GetOlympus\Zeus\Configuration;

use GetOlympus\Zeus\Configuration\ConfigurationInterface;
use GetOlympus\Zeus\Utils\Helpers;

/**
 * Configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

abstract class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $filepath = '';

    /**
     * Initialize filepath with configs.
     *
     * @param  string  $prepend
     * @param  array   $available
     *
     * @return array
     */
    protected function getFunctions($prepend, $available) : array
    {
        // Check filepath
        if (empty($this->filepath)) {
            return [];
        }

        // Get configurations
        $configs = include $this->filepath;

        // Check
        if (empty($configs)) {
            return [];
        }

        $functions = [];

        // Iterate on configs
        foreach ($configs as $key => $args) {
            if (is_null($args) || !in_array($key, $available)) {
                continue;
            }

            $functions[Helpers::toFunctionFormat($key).$prepend] = $args;
        }

        return $functions;
    }

    /**
     * Add resource path.
     *
     * @param  string  $filepath
     */
    public function setPath($filepath) : void
    {
        // Set file only if it exists
        if (!file_exists($file = realpath($filepath))) {
            return;
        }

        $this->filepath = $file;
    }

    /**
     * Add all usefull WP filters and hooks.
     */
    abstract public function init() : void;
}
