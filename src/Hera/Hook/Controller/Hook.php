<?php

namespace GetOlympus\Hera\Hook\Controller;

use GetOlympus\Hera\Base\Controller\Base;
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

class Hook extends Base implements HookInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new HookModel();
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
        $this->getModel()->setType($type);
        $this->getModel()->setIdentifier($identifier);
        $this->getModel()->setCallback($callback);
        $this->getModel()->setPriority($priority);
    }

    /**
     * Hook method.
     */
    public function callback()
    {
        $this->getModel()->runCallback();
    }

    /**
     * Define hook.
     *
     * @param null|mixed $args
     * @return void
     */
    public function listen($args = null)
    {
        if ('action' === $this->getModel()->getType()) {
            return do_action($this->getModel()->getIdentifier(), $args);
        }

        return apply_filters($this->getModel()->getIdentifier(), $args);
    }

    /**
     * Execute hook action/filter.
     *
     * @return void
     */
    public function run()
    {
        if ('action' === $this->getModel()->getType()) {
            return add_action($this->getModel()->getIdentifier(), [&$this, 'callback'], $this->getModel()->getPriority());
        }

        return add_filter($this->getModel()->getIdentifier(), [&$this, 'callback'], $this->getModel()->getPriority());
    }
}
