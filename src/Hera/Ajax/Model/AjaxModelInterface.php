<?php

namespace GetOlympus\Hera\Ajax\Model;

/**
 * Ajax model interface.
 *
 * @package Olympus Hera
 * @subpackage Ajax\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
 *
 */

interface AjaxModelInterface
{
    /**
     * Gets the value of callback.
     *
     * @return function
     */
    public function getCallback();

    /**
     * Sets the value of callback.
     *
     * @param function $callback the callback
     *
     * @return self
     */
    public function setCallback(function $callback);

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Sets the value of identifier.
     *
     * @param string $identifier the identifier
     *
     * @return self
     */
    public function setIdentifier($identifier);
}
