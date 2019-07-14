<?php

namespace GetOlympus\Zeus\Configuration\Implementation;

/**
 * Configuration implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.0
 *
 */

interface ConfigurationImplementation
{
    /**
     * Initialize filepath with configs.
     *
     * @param  string  $prepend
     * @param  array   $available
     * @return array   $functions
     */
    public function getFunctions($prepend, $available);

    /**
     * Add resource path.
     *
     * @param  string  $filepath
     */
    public function setPath($filepath);
}
