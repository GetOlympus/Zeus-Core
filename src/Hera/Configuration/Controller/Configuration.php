<?php

namespace GetOlympus\Hera\Configuration\Controller;

/**
 * Hera Configuration controller
 *
 * @package Olympus Hera
 * @subpackage Configuration\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

abstract class Configuration
{
    /**
     * @var string
     */
    protected $filepath = '';

    /**
     * Add all usefull WP filters and hooks.
     */
    abstract public function init();

    /**
     * Add resource path.
     *
     * @param string $filepath
     */
    public function setPath($filepath)
    {
        // Set file only if it exists
        if (!file_exists($file = realpath($filepath))) {
            return;
        }

        $this->filepath = $file;
    }
}
