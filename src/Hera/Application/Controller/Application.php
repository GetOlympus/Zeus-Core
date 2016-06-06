<?php

namespace GetOlympus\Hera\Application\Controller;

use GetOlympus\Hera\Application\Controller\ApplicationInterface;
use GetOlympus\Hera\Application\Exception\Application as ApplicationException;
use GetOlympus\Hera\Render\Controller\Render;
use League\Container\Container;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Hera Application controller
 *
 * @package Olympus Hera
 * @subpackage Application\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Application implements ApplicationInterface
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
     * @var array
     */
    protected $exceptions = [];

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $interfaces = [];

    /**
     * @var MapClassLoader
     */
    protected $loader = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initiate Container Dependency Injection
        $this->container = new Container;

        // Initialize all components
        $this->init();
    }

    /**
     * Initialize all components.
     */
    public function init()
    {
        // Get services
        $services = $this->getServices();

        // Register all
        foreach ($services as $alias => $service) {
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
    public function add($alias, $service = '', $args = [])
    {
        $service = empty($service) ? $alias : $service;

        // Register the service as a prototype
        if (!empty($args)) {
            $this->container->add($alias, $service)->withArgument($args);
        }
        else {
            $this->container->add($alias, $service);
        }
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
     * Get default components.
     *
     * @return array $components
     */
    public function getComponents()
    {
        return [
            'Ajax'                      => 'GetOlympus\Hera\Ajax\Controller\Ajax',
            'Configuration'             => 'GetOlympus\Hera\Configuration\Controller\Configuration',
            'Error'                     => 'GetOlympus\Hera\Error\Controller\Error',
            'Field'                     => 'GetOlympus\Hera\Field\Controller\Field',
            'Hook'                      => 'GetOlympus\Hera\Hook\Controller\Hook',
            'Menu'                      => 'GetOlympus\Hera\Menu\Controller\Menu',
            'Metabox'                   => 'GetOlympus\Hera\Metabox\Controller\Metabox',
            'Notification'              => 'GetOlympus\Hera\Notification\Controller\Notification',
            'Option'                    => 'GetOlympus\Hera\Option\Controller\Option',
            'Posttype'                  => 'GetOlympus\Hera\Posttype\Controller\Posttype',
            'PosttypeHook'              => 'GetOlympus\Hera\Posttype\Controller\PosttypeHook',
            'Render'                    => 'GetOlympus\Hera\Render\Controller\Render',
            'Request'                   => 'GetOlympus\Hera\Request\Controller\Request',
            'Template'                  => 'GetOlympus\Hera\Template\Controller\Template',
            'Term'                      => 'GetOlympus\Hera\Term\Controller\Term',
            'TermHook'                  => 'GetOlympus\Hera\Term\Controller\TermHook',
            'Translate'                 => 'GetOlympus\Hera\Translate\Controller\Translate',
            'WalkerSingle'              => 'GetOlympus\Hera\WalkerSingle\Controller\WalkerSingle',
            'Widget'                    => 'GetOlympus\Hera\Widget\Controller\Widget',
        ];
    }

    /**
     * Get default configurations.
     *
     * @return array $configurations
     */
    public function getConfigurations()
    {
        return [
            'MenusConfiguration'        => 'GetOlympus\Hera\Configuration\Controller\Menus',
            'SettingsConfiguration'     => 'GetOlympus\Hera\Configuration\Controller\Settings',
            'ShortcodesConfiguration'   => 'GetOlympus\Hera\Configuration\Controller\Shortcodes',
            'SidebarsConfiguration'     => 'GetOlympus\Hera\Configuration\Controller\Sidebars',
            'SizesConfiguration'        => 'GetOlympus\Hera\Configuration\Controller\Sizes',
            'SupportsConfiguration'     => 'GetOlympus\Hera\Configuration\Controller\Supports',
        ];
    }

    /**
     * Get default components.
     *
     * @return array $components
     */
    public function getServices()
    {
        // Get all services
        return array_merge($this->components, $this->getComponents(), $this->getConfigurations());
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
     * Initialize custom components.
     *
     * @param string    $classname
     * @param array     $components
     */
    public function initComponents($classname, $components = [])
    {
        // Check components
        if (empty($components)) {
            return;
        }

        $name = empty($classname) ? Render::urlize(get_class($this)) : $classname;

        // Check cache file
        if (!file_exists($maps = OLH_CACHE.$name.'-components.php')) {
            // Store all in cache file
            ClassMapGenerator::dump($components, OLH_CACHE.$name.'-components.php');
        }

        $classmap = include_once $maps;

        // Instanciate new ClassLoader to load components
        $this->loader = new MapClassLoader($classmap);

        // Register components
        $this->loader->register();

        // Initialize components
        foreach ($classmap as $alias => $path) {
            $this->add($alias);
            $service = $this->get($alias);
        }
    }

    /**
     * Initialize configs files containing theme definitions.
     *
     * @param array $args
     */
    public function initConfigs($args)
    {
        // Check for application
        if (empty($args)) {
            return;
        }

        // Get available configurations
        $available = $this->getConfigurations();

        // Iterate
        foreach ($args as $component => $file) {
            // Check if configuration asked exists
            if (!array_key_exists($component, $available)) {
                continue;
            }

            $service = $this->get($component);

            // Check service integrity
            if (!$service) {
                continue;
            }

            // Initialize service
            $service->setPath($file);
            $service->init();
        }
    }
}
