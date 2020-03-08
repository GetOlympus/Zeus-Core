<?php

namespace GetOlympus\Zeus\Application;

/**
 * Application interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Application
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface ApplicationInterface
{
    /**
     * Initialize all components.
     */
    public function init() : void;
}
