<?php

namespace GetOlympus\Hera\Ajax\Controller;

/**
 * Ajax interface.
 *
 * @package Olympus Hera
 * @subpackage Ajax\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface AjaxInterface
{
    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $callback
     */
    public function init($identifier, $callback);

    /**
     * Hook method.
     */
    public function callbackConnected();

    /**
     * Hook method.
     */
    public function callbackDisconnected();
}
