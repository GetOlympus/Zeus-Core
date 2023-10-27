<?php

namespace GetOlympus\Zeus\Application;

use Composer\Autoload\ClassLoader;
use Composer\Autoload\ClassMapGenerator;
use GetOlympus\Zeus\Application\ApplicationInterface;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Application controller
 *
 * @package    OlympusZeusCore
 * @subpackage Application
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Application implements ApplicationInterface
{
    /**
     * @var array
     */
    protected $adminscripts = [];

    /**
     * @var array
     */
    protected $adminstyles = [];

    /**
     * @var array
     */
    protected $available_components = [
        'adminpages', 'crons', 'customizers', 'posttypes', 'terms', 'users', 'walkers', 'widgets'
    ];

    /**
     * @var string
     */
    protected $classname;

    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var array
     */
    protected $configurations = [];

    /**
     * @var array
     */
    protected $controls = [];

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var ClassLoader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $locale = 'default';

    /**
     * @var array
     */
    protected $panels = [];

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * @var array
     */
    protected $translations = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize classname
        $class = new \ReflectionClass(get_class($this));
        $this->classname = strtolower($class->getShortName());

        // Initialize defaults
        $this->defaults['controls'] = isset($this->defaults['controls']) ? $this->defaults['controls'] : [];
        $this->defaults['fields']   = isset($this->defaults['fields'])   ? $this->defaults['fields']   : [];
        $this->defaults['panels']   = isset($this->defaults['panels'])   ? $this->defaults['panels']   : [];
        $this->defaults['sections'] = isset($this->defaults['sections']) ? $this->defaults['sections'] : [];
        $this->defaults['settings'] = isset($this->defaults['settings']) ? $this->defaults['settings'] : [];

        // Initialize ClassLoader
        $this->loader = new ClassLoader();

        // Initialize all components
        $this->setVars();
        $this->init();
    }

    /**
     * Enqueue scripts and styles.
     */
    protected function enqueueFiles() : void
    {
        $scripts = OL_ZEUS_ISADMIN ? $this->adminscripts : $this->scripts;
        $styles  = OL_ZEUS_ISADMIN ? $this->adminstyles : $this->styles;

        // Enqueu files in DIST folder
        if (OL_ZEUS_ISADMIN) {
            add_action('admin_enqueue_scripts', function () use ($scripts, $styles) {
                Helpers::enqueueFiles($scripts, 'js');
                Helpers::enqueueFiles($styles, 'css');
            });
        } else {
            Helpers::enqueueFiles($scripts, 'js');
            Helpers::enqueueFiles($styles, 'css');
        }
    }

    /**
     * Initialize all components.
     */
    public function init() : void
    {
        // Initialize controls
        $this->initControls();

        // Initialize fields
        $this->initFields();

        // Initialize panels
        $this->initPanels();

        // Initialize sections
        $this->initSections();

        // Initialize settings
        $this->initSettings();

        // Initialize translations
        $this->initTranslations();

        // Initialize configurations
        $this->initConfigurations();

        // Initialize components
        $this->initComponents();

        // Enqueue all
        $this->enqueueFiles();
    }

    /**
     * Initialize components.
     */
    protected function initComponents() : void
    {
        // Check components
        if (empty($this->components)) {
            return;
        }

        $components = $this->components;
        $this->components = [];

        // Works on each components
        foreach ($this->available_components as $component) {
            if (!isset($components[$component])) {
                continue;
            }

            $this->components[$component][] = $components[$component];
        }

        // Register components
        $maps = $this->registerPaths($this->components);

        if (empty($maps)) {
            return;
        }

        // Register ClassLoader
        $this->loader->register();

        // Works on maps
        foreach ($maps as $map) {
            // Define filter and priority
            $action        = $map['action'];
            $classmap      = $map['classmap'];
            $currentfilter = current_filter();
            $priority      = 'widgets' === $action ? 0 : 1;

            // Register post type
            if ('init' === $currentfilter) {
                // Already inside an `init` action
                $this->registerObjects($classmap, $action);
            } else {
                // Outside an `init` action
                add_action('init', function () use ($classmap, $action) {
                    $this->registerObjects($classmap, $action);
                }, $priority);
            }
        }
    }

    /**
     * Initialize configurations files.
     */
    protected function initConfigurations() : void
    {
        // Fix WordPress bug
        if (!current_theme_supports('widgets') && is_customize_preview()) {
            add_theme_support('widgets');
        }

        // Check configurations
        if (empty($this->configurations)) {
            return;
        }

        // Get all configurations
        $namespace = 'GetOlympus\\Zeus\\Configuration\\Configs\\';
        $classmap = ClassMapGenerator::createMap(OL_ZEUS_PATH.'src'.S.'Zeus'.S.'Configuration'.S.'Configs');

        // Iterate
        foreach ($this->configurations as $component => $object) {
            $component = $namespace.$component;

            // Check if configuration asked exists
            if (!array_key_exists($component, $classmap)) {
                continue;
            }

            // Initialize configuration
            $config = new $component();
            $config->setConfigurations($object);
            $config->init();
        }
    }

    /**
     * Initialize controls.
     */
    protected function initControls() : void
    {
        // Initialize controls
        $this->controls = $this->initVars($this->controls, 'controls');

        // Check controls
        if (empty($this->controls)) {
            return;
        }

        // Iterate
        foreach ($this->controls as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $t = $class::translate();
            $this->translations = array_merge($this->translations, $t);
        }
    }

    /**
     * Initialize fields.
     */
    protected function initFields() : void
    {
        // Check fields
        if (empty($this->fields)) {
            $this->fields = $this->initVars([], 'fields');
            return;
        }

        $fields       = [];
        $translations = [];

        // Build fiels to initialize and to translate
        foreach ($this->fields as $class => $file) {
            if (is_int($class)) {
                $translations[] = $file;
                continue;
            }

            $fields[$class]  = $file;
            $translations[] = $class;
        }

        // Initialize fields
        $fields = $this->initVars($fields, 'fields');

        // Check translations
        if (empty($translations)) {
            return;
        }

        // Translate fields
        foreach ($translations as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $t = $class::translate();
            $this->translations = array_merge($this->translations, $t);
        }
    }

    /**
     * Initialize panels.
     */
    protected function initPanels() : void
    {
        // Initialize panels
        $this->panels = $this->initVars($this->panels, 'panels');

        // Check panels
        if (empty($this->panels)) {
            return;
        }

        // Iterate
        foreach ($this->panels as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $t = $class::translate();
            $this->translations = array_merge($this->translations, $t);
        }
    }

    /**
     * Initialize sections.
     */
    protected function initSections() : void
    {
        // Initialize sections
        $this->sections = $this->initVars($this->sections, 'sections');

        // Check sections
        if (empty($this->sections)) {
            return;
        }

        // Iterate
        foreach ($this->sections as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $t = $class::translate();
            $this->translations = array_merge($this->translations, $t);
        }
    }

    /**
     * Initialize settings.
     */
    protected function initSettings() : void
    {
        // Initialize settings
        $this->settings = $this->initVars($this->settings, 'settings');

        // Check settings
        if (empty($this->settings)) {
            return;
        }

        // Iterate
        foreach ($this->settings as $class) {
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
    protected function initTranslations() : void
    {
        // Get all translations with default MO file
        Translate::l($this->translations, $this->locale);
    }

    /**
     * Initialize variable.
     *
     * @param  array   $vars
     * @param  string  $component
     *
     * @return array
     */
    protected function initVars($vars, $component = 'controls') : array
    {
        if (isset($this->components[$component])) {
            $vars = empty($vars) ? $this->components[$component] : $vars;
        }

        $vars = is_string($vars) ? [$vars] : $vars;
        $vars = array_merge($this->defaults[$component], $vars);

        $maps = $this->registerPaths([$component => $vars]);

        if (empty($maps)) {
            return [];
        }

        $vars = [];

        foreach ($maps[0]['classmap'] as $classname => $file) {
            $vars[] = $classname;
        }

        return $vars;
    }

    /**
     * Register post types / terms / and more.
     *
     * @param  array   $classmap
     * @param  string  $action
     */
    protected function registerObjects($classmap, $action) : void
    {
        foreach ($classmap as $service => $file) {
            if ('customizers' !== $action) {
                new $service();
                continue;
            }

            new $service([
                'controls' => $this->controls,
                'panels'   => $this->panels,
                'sections' => $this->sections,
                'settings' => $this->settings,
            ]);
        }
    }

    /**
     * Register components.
     *
     * @param  array   $paths
     *
     * @return array
     */
    protected function registerPaths($paths) : array
    {
        // Check objects
        if (empty($paths)) {
            return [];
        }

        $cacheprefix = (defined('CACHEPATH') ? CACHEPATH : OL_ZEUS_PATH.'app'.S.'cache'.S).$this->classname.'-';
        $cachesuffix = '-components.php';

        $loader = [];
        $maps   = [];

        // Work on file paths
        foreach ($paths as $action => $files) {
            // Remove non-existent files
            foreach ($files as $k => $file) {
                if (!is_dir($file)) {
                    unset($files[$k]);
                }

                continue;
            }

            // Check files
            if (empty($files)) {
                continue;
            }

            // Work on cache file name
            $filepath = $cacheprefix.$action.$cachesuffix;

            // Check cache file & Store all in cache file
            if (!file_exists($filepath)) {
                ClassMapGenerator::dump($files, $filepath);
            }

            $classmap = include_once $filepath;

            // Update loader
            $loader = array_merge($loader, $classmap);

            // Get classes
            $maps[] = [
                'action'   => $action,
                'classmap' => $classmap,
            ];
        }

        if (empty($maps)) {
            return [];
        }

        $this->loader->addClassMap($loader);

        return $maps;
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars();
}
