<?php

namespace GetOlympus\Hera\Ajax\Model;

/**
 * Abstract class to define Ajax model.
 *
 * @package Olympus Hera
 * @subpackage Ajax\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Ajax
{
    /**
     * @var function
     */
    protected $callback;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Gets the value of callback.
     *
     * @return function
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the value of callback.
     *
     * @param function $callback the callback
     *
     * @return self
     */
    public function setCallback(function $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the value of identifier.
     *
     * @param string $identifier the identifier
     *
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }
}
