<?php

namespace GetOlympus\Zeus\Application\Controller;

/**
 * Application interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Application\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface ApplicationInterface
{
    /**
     * Initialize all components.
     */
    public function init();

    /**
     * Register a service.
     *
     * @param string $service
     * @param string $alias
     * @param mixed $args
     */
    public function add($service, $alias = '', $args = []);

    /**
     * Get the asked service.
     *
     * @param string $service
     * @return object $service
     */
    public function get($service);

    /**
     * Get default components.
     *
     * @return array $components
     */
    public function getComponents();

    /**
     * Get default configurations.
     *
     * @return array $configurations
     */
    public function getConfigurations();

    /**
     * Get default components.
     *
     * @return array $components
     */
    public function getServices();

    /**
     * Check if the asked service is set or not.
     *
     * @param string $service
     * @return boolean $service
     */
    public function has($service);

    /**
     * Initialize configs files containing theme definitions.
     */
    public function initConfigs();

    /**
     * Register components
     */
    public function registerComponents();

    /**
     * Register post types / terms / and more.
     *
     * @param array $classmap
     */
    public function registerObjects($classmap);

    /**
     * Register widgets.
     *
     * @param array $classmap
     */
    public function registerWidgets($classmap);
}
