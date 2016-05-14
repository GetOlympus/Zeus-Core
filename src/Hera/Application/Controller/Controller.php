<?php

namespace GetOlympus\Hera\Application\Controller;

use League\Container\Container;

/**
 * Hera Application controller
 *
 * @package Olympus Hera
 * @subpackage Application\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Controller
{
    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var Hera
     */
    protected static $instance = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Load all components
        $this->load();
    }

    /**
     * Get the asked service.
     *
     * @param string $service
     * @return object $service
     */
    public function get($service)
    {
        // Return the asked service
        return $this->container->get($service);
    }

    /**
     * Gets the value of instance.
     *
     * @return Hera
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Manage all dependencies via Container.
     */
    protected function load()
    {
        // Initiate Container Dependency Injection
        $this->container = new Container;

        // Components to load
        $components = [
            // Hera core components
            'Ajax'          => 'GetOlympus\Hera\Ajax\Controller\Ajax',
            'Error'         => 'GetOlympus\Hera\Error\Controller\Error',
            'Field'         => 'GetOlympus\Hera\Field\Controller\Field',
            'Hook'          => 'GetOlympus\Hera\Hook\Controller\Hook',
            'Menu'          => 'GetOlympus\Hera\Menu\Controller\Menu',
            'Metabox'       => 'GetOlympus\Hera\Metabox\Controller\Metabox',
            'Notification'  => 'GetOlympus\Hera\Notification\Controller\Notification',
            'Option'        => 'GetOlympus\Hera\Option\Controller\Option',
            'Posttype'      => 'GetOlympus\Hera\Posttype\Controller\Posttype',
            'PosttypeHook'  => 'GetOlympus\Hera\Posttype\Controller\PosttypeHook',
            'Render'        => [
                'name' => 'GetOlympus\Hera\Render\Controller\Render',
                'args' => $this->components
            ],
            'Request'       => 'GetOlympus\Hera\Request\Controller\Request',
            'Template'      => 'GetOlympus\Hera\Template\Controller\Template',
            'Term'          => 'GetOlympus\Hera\Term\Controller\Term',
            'TermHook'      => 'GetOlympus\Hera\Term\Controller\TermHook',
            'Translate'     => 'GetOlympus\Hera\Translate\Controller\Translate',
            'WalkerSingle'  => 'GetOlympus\Hera\WalkerSingle\Controller\WalkerSingle',
            'Widget'        => 'GetOlympus\Hera\Widget\Controller\Widget',
        ];

        // Merge with field and external components
        $components = array_merge($components, $this->components);

        // Check components
        if (empty($components)) {
            return;
        }

        // Register all
        foreach ($components as $alias => $service) {
            if (is_array($service)) {
                $this->register($alias, $service['name'], $service['args']);
            }
            else {
                $this->register($alias, $service);
            }
        }
    }

    /**
     * Register a service.
     *
     * @param string $alias
     * @param string $service
     * @param mixed $args
     */
    private function register($alias, $service, $args = [])
    {
        // Register the service as a prototype
        if (empty($args)) {
            $this->container->add($alias, $service);
        }
        else {
            $this->container->add($alias, $service)->withArgument($args);
        }
    }

    /**
     * Set external components.
     *
     * @param string $alias
     * @param string $service
     */
    public function setComponent($alias, $service)
    {
        // Register component
        $this->register($alias, $service);
    }
}
