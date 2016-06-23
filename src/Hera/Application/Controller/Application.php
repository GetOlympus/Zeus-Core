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
     * @var string
     */
    protected $classname;

    /**
     * @var array
     */
    protected $configurations = [];

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
    protected $externals = [];

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $widgets = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initiate Container Dependency Injection
        $this->container = new Container;

        // Initialize classname
        $class = new \ReflectionClass(get_class($this));
        $this->classname = strtolower($class->getShortName());

        // Initialize all components
        $this->setExternals();
        $this->setVars();
        $this->init();
    }

    /**
     * Initialize all components.
     */
    public function init()
    {
        // Get services
        $services = $this->getServices();
        $externals = $this->externals;

        // Register all
        foreach ($services as $alias => $service) {
            $this->add($service, $alias);
        }

        // Register configurations
        $this->initConfigs();

        // Register post types / terms / widgets / admin pages and more
        $this->registerComponents();
    }

    /**
     * Register a service.
     *
     * @param string $service
     * @param string $alias
     * @param mixed $args
     */
    public function add($service, $alias = '', $args = [])
    {
        // Register the service as a prototype
        if (!empty($args)) {
            if (empty($alias)) {
                $this->container->add($service)->withArgument($args);
            } else {
                $this->container->add($alias, $service)->withArgument($args);
            }
        } else {
            if (empty($alias)) {
                $this->container->add($service);
            } else {
                $this->container->add($alias, $service);
            }
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
        $components = [
            'Base'                      => 'GetOlympus\Hera\Base\Controller\Base',
            'BaseWidget'                => 'GetOlympus\Hera\Base\Controller\BaseWidget',

            'AdminPage'                 => 'GetOlympus\Hera\AdminPage\Controller\AdminPage',
            'Ajax'                      => 'GetOlympus\Hera\Ajax\Controller\Ajax',
            'Configuration'             => 'GetOlympus\Hera\Configuration\Controller\Configuration',
            'Error'                     => 'GetOlympus\Hera\Error\Controller\Error',
            'Field'                     => 'GetOlympus\Hera\Field\Controller\Field',
            'Hook'                      => 'GetOlympus\Hera\Hook\Controller\Hook',
            'Metabox'                   => 'GetOlympus\Hera\Metabox\Controller\Metabox',
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

        // Iterate on externals to add "Field" suffix
        foreach ($this->externals as $shortname => $classname) {
            $components[$shortname.'Field'] = $classname;
        }

        return $components;
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
     * Initialize configs files containing theme definitions.
     */
    public function initConfigs()
    {
        // Check configurations
        if (empty($this->configurations)) {
            return;
        }

        // Get available configurations
        $available = $this->getConfigurations();

        // Iterate
        foreach ($this->configurations as $component => $file) {
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

    /**
     * Register components
     */
    public function registerComponents()
    {
        // Check objects
        if (empty($this->paths)) {
            return;
        }

        // Work on file paths
        foreach ($this->paths as $action => $paths) {
            // Work on paths
            $paths = !is_array($paths) ? [$paths] : $paths;

            // Work on file name
            $filepath = OLH_CACHE.$this->classname.'-'.$action.'-components.php';

            // Check cache file
            if (!file_exists($filepath)) {
                // Store all in cache file
                ClassMapGenerator::dump($paths, $filepath);
            }

            $classmap = include_once $filepath;

            // Instanciate new ClassLoader to load and register components
            $loader = new MapClassLoader($classmap);
            $loader->register();

            // Get current hook
            $current = current_filter();
            $function = in_array($action, ['widgets_init']) ? 'registerWidgets' : 'registerObjects';

            // Register post type
            if ($action === $current) {
                // Already inside an `init` action
                $this->$function($classmap);
            } else {
                // Outside an `init` action
                add_action($action, function () use ($classmap, $function){
                    $this->$function($classmap);
                });
            }
        }
    }

    /**
     * Register post types / terms / and more.
     *
     * @param array $classmap
     */
    public function registerObjects($classmap)
    {
        foreach ($classmap as $service => $file) {
            $this->add($service);
            $component = $this->get($service);
        }
    }

    /**
     * Register widgets.
     *
     * @param array $classmap
     */
    public function registerWidgets($classmap)
    {
        foreach ($classmap as $service => $file) {
            $this->add($service);
            register_widget($service);
        }
    }

    /**
     * Prepare externals.
     */
    abstract protected function setExternals();

    /**
     * Prepare variables.
     */
    abstract protected function setVars();
}
