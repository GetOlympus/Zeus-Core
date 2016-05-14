<?php

namespace GetOlympus\Hera\Hook\Model;

/**
 * Abstract class to define Hook model.
 *
 * @package Olympus Hera
 * @subpackage Hook\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Hook
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
     * @var integer
     */
    protected $priority;

    /**
     * @var string
     */
    protected $type;

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
     * Gets the value of callback.
     */
    public function runCallback()
    {
        $callback = $this->getCallback();
        call_user_func($callback);
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

    /**
     * Gets the value of priority.
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the value of priority.
     *
     * @param integer $priority the priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = 'action' === $type ? 'action' : 'filter';

        return $this;
    }
}
