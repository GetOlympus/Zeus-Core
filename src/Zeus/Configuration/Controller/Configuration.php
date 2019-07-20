<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Implementation\ConfigurationImplementation;
use GetOlympus\Zeus\Helpers\Controller\Helpers;

/**
 * Configuration controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

abstract class Configuration implements ConfigurationImplementation
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
    public function getFunctions($prepend, $available)
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
    public function setPath($filepath)
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
    abstract public function init();
}
