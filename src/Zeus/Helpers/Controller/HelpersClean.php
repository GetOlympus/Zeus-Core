<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Clean helper controller
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

abstract class HelpersClean
{
    /**
     * Add all usefull WP filters and hooks.
     *
     * @param array $args
     */
    abstract public function init($args);
}
