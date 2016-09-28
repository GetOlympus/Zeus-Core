<?php

namespace GetOlympus\Hera\Hook\Model;

/**
 * Hook model interface.
 *
 * @package Olympus Hera
 * @subpackage Hook\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
 *
 */

interface HookModelInterface
{
    /**
     * Gets the value of callback.
     *
     * @return string|array
     */
    public function getCallback();

    /**
     * Gets the value of callback.
     */
    public function runCallback();

    /**
     * Sets the value of callback.
     *
     * @param string|array $callback the callback
     *
     * @return self
     */
    public function setCallback($callback);

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

    /**
     * Gets the value of priority.
     *
     * @return integer
     */
    public function getPriority();

    /**
     * Sets the value of priority.
     *
     * @param integer $priority the priority
     *
     * @return self
     */
    public function setPriority($priority);

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the value of type.
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType($type);
}
