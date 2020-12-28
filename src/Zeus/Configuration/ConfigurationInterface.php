<?php

namespace GetOlympus\Zeus\Configuration;

/**
 * Configuration interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.0
 *
 */

interface ConfigurationInterface
{
    /**
     * Add configurations from path or array.
     *
     * @param  mixed   $object
     */
    public function setConfigurations($object) : void;
}
