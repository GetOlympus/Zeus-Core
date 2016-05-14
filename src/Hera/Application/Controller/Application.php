<?php

namespace GetOlympus\Hera\Application\Controller;

use Composer\Autoload\ClassLoader;
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

abstract class Application
{
    /**
     * @var array
     */
    protected $components = [
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
        'Render'        => 'GetOlympus\Hera\Render\Controller\Render',
        'Request'       => 'GetOlympus\Hera\Request\Controller\Request',
        'Template'      => 'GetOlympus\Hera\Template\Controller\Template',
        'Term'          => 'GetOlympus\Hera\Term\Controller\Term',
        'TermHook'      => 'GetOlympus\Hera\Term\Controller\TermHook',
        'Translate'     => 'GetOlympus\Hera\Translate\Controller\Translate',
        'WalkerSingle'  => 'GetOlympus\Hera\WalkerSingle\Controller\WalkerSingle',
        'Widget'        => 'GetOlympus\Hera\Widget\Controller\Widget',
    ];

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var ClassLoader
     */
    protected $loader = null;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initiate Container Dependency Injection
        $this->container = new Container;

        // Instanciate new ClassLoader to load components
        $this->loader = new ClassLoader;

        // Register all
        foreach ($this->components as $alias => $service) {
            $this->add($alias, $service);
        }
    }

    /**
     * Register a service.
     *
     * @param string $alias
     * @param string $service
     * @param mixed $args
     */
    private function add($alias, $service, $args = [])
    {
        // Register the service as a prototype
        if (!empty($args)) {
            $this->container->add($alias, $service)->withArgument($args);
        }
        else {
            $this->container->add($alias, $service);
        }
    }

    /**
     * Add resource path.
     *
     * @param string $alias
     * @param string $namespace
     * @param array $path
     * @param boolean $prepend
     * @param array $args
     */
    public function addPath($alias, $namespace, $path, $prepend = false, $args = [])
    {
        // Check namespace
        $namespace = empty($namespace) ? get_class($this) : $namespace;

        // Instanciate only known classes
        if (array_key_exists($alias, $this->components)) {
            return;
        }

        // Autoload component
        $this->loader->addPsr4($namespace, $path, $prepend);
        $this->add($alias, $namespace, $args);
    }

    /**
     * Get the asked service.
     *
     * @param string $service
     * @return object $service
     */
    public function get($service)
    {
        return $this->has($service) ? $this->container->get($service) : null;
    }

    /**
     * Check if the asked service is set or not.
     *
     * @param string $service
     * @return boolean $service
     */
    public function has($service)
    {
        return $this->container->has($service);
    }

    /**
     * Set resources path.
     */
    public function register()
    {
        // Register all components
        $this->loader->register();
    }
}
