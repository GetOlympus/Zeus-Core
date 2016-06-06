<?php

namespace GetOlympus\Hera\Hook\Controller;

use GetOlympus\Hera\Hook\Controller\HookInterface;
use GetOlympus\Hera\Hook\Model\HookModel;

/**
 * Gets its own hooks.
 *
 * @package Olympus Hera
 * @subpackage Hook\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Hook implements HookInterface
{
    /**
     * @var HookModel
     */
    protected $hook;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hook = new HookModel();
    }

    /**
     * Initialize all data.
     *
     * @param string $type
     * @param string $identifier
     * @param string|array $callback
     * @param mixed $priority
     */
    public function initialize($type, $identifier, $callback, $priority = 10)
    {
        $this->hook->setType($type);
        $this->hook->setIdentifier($identifier);
        $this->hook->setCallback($callback);
        $this->hook->setPriority($priority);
    }

    /**
     * Hook method.
     */
    public function callback()
    {
        $this->hook->runCallback();
    }

    /**
     * Define hook.
     *
     * @param null|mixed $args
     * @return void
     */
    public function listen($args = null)
    {
        if ('action' === $this->hook->getType()) {
            return do_action($this->hook->getIdentifier(), $args);
        }

        return apply_filters($this->hook->getIdentifier(), $args);
    }

    /**
     * Execute hook action/filter.
     *
     * @return void
     */
    public function run()
    {
        if ('action' === $this->hook->getType()) {
            return add_action($this->hook->getIdentifier(), [&$this, 'callback'], $this->hook->getPriority());
        }

        return add_filter($this->hook->getIdentifier(), [&$this, 'callback'], $this->hook->getPriority());
    }
}
