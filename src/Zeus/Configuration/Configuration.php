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
     * @var array
     */
    protected $configurations = [];

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
        // Check configurations
        if (empty($this->configurations)) {
            return [];
        }

        $functions = [];

        // Iterate on configs
        foreach ($this->configurations as $key => $args) {
            if (is_null($args) || !in_array($key, $available)) {
                continue;
            }

            $functions[Helpers::toFunctionFormat($key).$prepend] = $args;
        }

        return $functions;
    }

    /**
     * Add configurations from path or array.
     *
     * @param  mixed   $object
     */
    public function setConfigurations($object) : void
    {
        // Set configurations only if object is an array or a filepath
        if (!is_array($object) && !is_string($object)) {
            return;
        }

        // Set confgirations from file only if it exists
        if (is_string($object)) {
            $file = realpath($object);
            $this->configurations = file_exists($file) ? include $file : [];

            return;
        }

        // Array case
        $this->configurations = is_array($object) ? $object : [];
    }

    /**
     * Add all usefull WP filters and hooks.
     */
    abstract public function init() : void;
}
