<?php

namespace GetOlympus\Hera\Ajax\Controller;

use GetOlympus\Hera\Ajax\Controller\AjaxInterface;
use GetOlympus\Hera\Ajax\Model\AjaxModel;
use GetOlympus\Hera\Base\Controller\Base;
use GetOlympus\Hera\Hook\Controller\Hook;

/**
 * Gets its own ajax call.
 *
 * @package Olympus Hera
 * @subpackage Ajax\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Ajax extends Base implements AjaxInterface
{
    /**
     * @var Hook
     */
    protected $hook_connected;

    /**
     * @var Hook
     */
    protected $hook_disconnected;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new AjaxModel();
    }

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $callback
     */
    public function init($identifier, $callback)
    {
        $this->getModel()->setIdentifier($identifier);
        $this->getModel()->setCallback($callback);

        $id = $this->getModel()->getIdentifier();

        $this->hook_connected = new Hook('wp_ajax_'.$id, array($this, 'callbackConnected'));
        $this->hook_disconnected = new Hook('wp_ajax_nopriv_'.$id, array($this, 'callbackDisconnected'));
    }

    /**
     * Hook method.
     */
    public function callbackConnected()
    {
        $this->hook_connected->runCallback();
    }

    /**
     * Hook method.
     */
    public function callbackDisconnected()
    {
        $this->hook_disconnected->runCallback();
    }
}
