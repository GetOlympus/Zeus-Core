<?php

namespace GetOlympus\Hera\Ajax\Controller;

use GetOlympus\Hera\Ajax\Controller\AjaxInterface;
use GetOlympus\Hera\Ajax\Model\AjaxModel;
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

class Ajax implements AjaxInterface
{
    /**
     * @var AjaxModel
     */
    protected $ajax;

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
        $this->ajax = new AjaxModel();
    }

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $callback
     */
    public function init($identifier, $callback)
    {
        $this->ajax->setIdentifier($identifier);
        $this->ajax->setCallback($callback);

        $id = $this->ajax->getIdentifier();

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
