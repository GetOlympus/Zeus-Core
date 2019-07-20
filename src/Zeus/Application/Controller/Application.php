<?php

namespace GetOlympus\Zeus\Application\Controller;

use GetOlympus\Zeus\Application\Implementation\ApplicationImplementation;
use GetOlympus\Zeus\Translate\Controller\Translate;
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

abstract class Application implements ApplicationImplementation
{
    /**
     * @var array
     */
    protected $adminpages = [];

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
    protected $crons = [];

    /**
     * @var array
     */
    protected $customizers = [];

    /**
     * @var array
     */
    protected $defaultfields = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $locale = 'default';

    /**
     * @var array
     */
    protected $posttypes = [];

    /**
     * @var array
     */
    protected $terms = [];

    /**
     * @var array
     */
    protected $translations = [];

    /**
     * @var array
     */
    protected $users = [];

    /**
     * @var array
     */
    protected $walkers = [];

    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize classname
        $class = new \ReflectionClass(get_class($this));
        $this->classname = strtolower($class->getShortName());

        // Initialize all components
        $this->setVars();
        $this->init();
    }

    /**
     * Initialize all components.
     */
    public function init()
    {
        // Initialize fields
        $this->initFields();

        // Initialize translations
        $this->initTranslations();

        // Initialize configurations
        $this->initConfigs();

        // Initialize components
        $this->initComponents();
    }

    /**
     * Initialize components.
     */
    public function initComponents()
    {
        // Works on all vars
        $this->adminpages  = !is_array($this->adminpages) ? [$this->adminpages] : $this->adminpages;
        $this->crons       = !is_array($this->crons) ? [$this->crons] : $this->crons;
        $this->customizers = !is_array($this->customizers) ? [$this->customizers] : $this->customizers;
        $this->posttypes   = !is_array($this->posttypes) ? [$this->posttypes] : $this->posttypes;
        $this->terms       = !is_array($this->terms) ? [$this->terms] : $this->terms;
        $this->users       = !is_array($this->users) ? [$this->users] : $this->users;
        $this->widgets     = !is_array($this->widgets) ? [$this->widgets] : $this->widgets;

        // Build main paths
        $paths = [
            'adminpages'  => $this->adminpages,
            'crons'       => $this->crons,
            'customizers' => $this->customizers,
            'posttypes'   => $this->posttypes,
            'terms'       => $this->terms,
            'users'       => $this->users,
            'widgets'     => $this->widgets
        ];

        // Register post types / terms / widgets / admin pages and more
        $this->registerComponents($paths);
    }

    /**
     * Initialize configurations files.
     */
    public function initConfigs()
    {
        // Check configurations
        if (empty($this->configurations)) {
            return;
        }

        // Get all configurations
        $namespace = 'GetOlympus\\Zeus\\Configuration\\Controller\\';
        $classmap = ClassMapGenerator::createMap(OL_ZEUS_PATH.S.'Configuration'.S.'Controller');

        // Iterate
        foreach ($this->configurations as $component => $file) {
            $component = $namespace.$component;

            // Check if configuration asked exists
            if (!array_key_exists($component, $classmap)) {
                continue;
            }

            // Initialize configuration
            $config = new $component();
            $config->setPath($file);
            $config->init();
        }
    }

    /**
     * Initialize fields.
     */
    public function initFields()
    {
        // Merge fields
        $this->fields = array_merge($this->defaultfields, $this->fields);

        // Check fields
        if (empty($this->fields)) {
            return;
        }

        // Iterate
        foreach ($this->fields as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $t = $class::translate();
            $this->translations = array_merge($this->translations, $t);
        }
    }

    /**
     * Initialize translations.
     */
    public function initTranslations()
    {
        // Add Zeus core translation
        $this->translations = array_merge(['olympus-zeus' => OL_ZEUS_LANGUAGES], $this->translations);

        // Get all translations with default MO file
        Translate::l($this->translations, $this->locale);
    }

    /**
     * Register components.
     *
     * @param  array   $paths
     */
    public function registerComponents($paths)
    {
        // Check objects
        if (empty($paths)) {
            return;
        }

        $cacheprefix = OL_ZEUS_CACHE.$this->classname.'-';
        $cachesuffix = '-components.php';

        // Work on file paths
        foreach ($paths as $action => $actionPaths) {
            if (empty($actionPaths)) {
                continue;
            }

            // Work on cache file name
            $filepath = $cacheprefix.$action.$cachesuffix;

            // Check cache file
            if (!file_exists($filepath)) {
                // Store all in cache file
                ClassMapGenerator::dump($actionPaths, $filepath);
            }

            // Get classes
            $classmap = include_once $filepath;

            // Instanciate new ClassLoader to load and register components
            $loader = new MapClassLoader($classmap);
            $loader->register();

            // Define filter and action
            $currentfilter = current_filter();
            $filter = 'widgets' === $action ? 'widgets_init' : ('adminpages' === $action ? 'admin_menu' : 'init');

            // Get current hook
            $function = 'widgets' === $action ? 'registerWidgets' : 'registerObjects';

            // Register post type
            if ($filter === $currentfilter) {
                // Already inside an `init` action
                $this->$function($classmap);
            } else {
                // Outside an `init` action
                add_action($filter, function () use ($classmap, $function) {
                    $this->$function($classmap);
                });
            }
        }
    }

    /**
     * Register post types / terms / and more.
     *
     * @param  array   $classmap
     */
    public function registerObjects($classmap)
    {
        foreach ($classmap as $service => $file) {
            new $service();
        }
    }

    /**
     * Register widgets.
     *
     * @param  array   $classmap
     */
    public function registerWidgets($classmap)
    {
        foreach ($classmap as $service => $file) {
            register_widget($service);
        }
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars();
}
