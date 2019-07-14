<?php

namespace GetOlympus\Zeus\Application\Implementation;

/**
 * Application implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Application\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface ApplicationImplementation
{
    /**
     * Initialize all components.
     */
    public function init();

    /**
     * Initialize components.
     */
    public function initComponents();

    /**
     * Initialize configurations files.
     */
    public function initConfigs();

    /**
     * Initialize fields.
     */
    public function initFields();

    /**
     * Initialize translations.
     */
    public function initTranslations();

    /**
     * Register components.
     *
     * @param  array   $paths
     */
    public function registerComponents($paths);

    /**
     * Register post types / terms / and more.
     *
     * @param  array   $classmap
     */
    public function registerObjects($classmap);

    /**
     * Register widgets.
     *
     * @param  array   $classmap
     */
    public function registerWidgets($classmap);
}
