<?php

namespace GetOlympus\Zeus\Application\Controller;

use GetOlympus\Zeus\Application\Controller\ApplicationInterface;
use GetOlympus\Zeus\Application\Exception\Application as ApplicationException;
use GetOlympus\Zeus\Render\Controller\Render;
use League\Container\Container;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Application controller
 *
 * @package    OlympusZeusCore
 * @subpackage Application\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
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
    protected $internals = [];

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
            'Base'                          => 'GetOlympus\Zeus\Base\Controller\Base',
            'BaseWidget'                    => 'GetOlympus\Zeus\Base\Controller\BaseWidget',
            'Helpers'                       => 'GetOlympus\Zeus\Helpers\Controller\Helpers',

            'AdminPage'                     => 'GetOlympus\Zeus\AdminPage\Controller\AdminPage',
            'Ajax'                          => 'GetOlympus\Zeus\Ajax\Controller\Ajax',
            'Configuration'                 => 'GetOlympus\Zeus\Configuration\Controller\Configuration',
            'Cron'                          => 'GetOlympus\Zeus\Cron\Controller\Cron',
            'Error'                         => 'GetOlympus\Zeus\Error\Controller\Error',
            'Field'                         => 'GetOlympus\Zeus\Field\Controller\Field',
            'Hook'                          => 'GetOlympus\Zeus\Hook\Controller\Hook',
            'Metabox'                       => 'GetOlympus\Zeus\Metabox\Controller\Metabox',
            'Option'                        => 'GetOlympus\Zeus\Option\Controller\Option',
            'Posttype'                      => 'GetOlympus\Zeus\Posttype\Controller\Posttype',
            'PosttypeHook'                  => 'GetOlympus\Zeus\Posttype\Controller\PosttypeHook',
            'Render'                        => 'GetOlympus\Zeus\Render\Controller\Render',
            'Request'                       => 'GetOlympus\Zeus\Request\Controller\Request',
            'Template'                      => 'GetOlympus\Zeus\Template\Controller\Template',
            'Term'                          => 'GetOlympus\Zeus\Term\Controller\Term',
            'TermHook'                      => 'GetOlympus\Zeus\Term\Controller\TermHook',
            'Translate'                     => 'GetOlympus\Zeus\Translate\Controller\Translate',
            'WalkerSingle'                  => 'GetOlympus\Zeus\WalkerSingle\Controller\WalkerSingle',
            'Widget'                        => 'GetOlympus\Zeus\Widget\Controller\Widget',
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
            'AccessManagementConfiguration' => 'GetOlympus\Zeus\Configuration\Controller\AccessManagement',
            'AdminThemesConfiguration'      => 'GetOlympus\Zeus\Configuration\Controller\AdminThemes',
            'AssetsConfiguration'           => 'GetOlympus\Zeus\Configuration\Controller\Assets',
            'CleanConfiguration'            => 'GetOlympus\Zeus\Configuration\Controller\Clean',
            'MenusConfiguration'            => 'GetOlympus\Zeus\Configuration\Controller\Menus',
            'SettingsConfiguration'         => 'GetOlympus\Zeus\Configuration\Controller\Settings',
            'ShortcodesConfiguration'       => 'GetOlympus\Zeus\Configuration\Controller\Shortcodes',
            'SidebarsConfiguration'         => 'GetOlympus\Zeus\Configuration\Controller\Sidebars',
            'SizesConfiguration'            => 'GetOlympus\Zeus\Configuration\Controller\Sizes',
            'SupportsConfiguration'         => 'GetOlympus\Zeus\Configuration\Controller\Supports',
        ];
    }

    /**
     * Get default components.
     *
     * @return array $components
     */
    public function getServices()
    {
        return array_merge($this->getComponents(), $this->getConfigurations());
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
            $filepath = OL_ZEUS_CACHE.$this->classname.'-'.$action.'-components.php';

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
                add_action($action, function () use ($classmap, $function) {
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
