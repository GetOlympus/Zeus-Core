<?php

namespace GetOlympus\Zeus\Hook;

/**
 * Hook model.
 *
 * @package    OlympusZeusCore
 * @subpackage Hook
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class HookModel
{
    /**
     * @var mixed
     */
    protected $callback;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * Gets the value of callback.
     *
     * @return mixed
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
     * @param  mixed   $callback
     */
    public function setCallback($callback) : void
    {
        $this->callback = $callback;
    }

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    /**
     * Sets the value of identifier.
     *
     * @param  string  $identifier
     */
    public function setIdentifier($identifier) : void
    {
        $this->identifier = $identifier;
    }

    /**
     * Gets the value of priority.
     *
     * @return int
     */
    public function getPriority() : int
    {
        return $this->priority;
    }

    /**
     * Sets the value of priority.
     *
     * @param  int     $priority
     */
    public function setPriority($priority) : void
    {
        $this->priority = $priority;
    }

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param  string  $type
     */
    public function setType($type) : void
    {
        $this->type = 'action' === $type ? 'action' : 'filter';
    }
}
