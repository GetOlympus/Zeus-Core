<?php

namespace GetOlympus\Hera\Application\Controller;

/**
 * Application interface.
 *
 * @package Olympus Hera
 * @subpackage Application\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
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
     * @param string $alias
     * @param string $service
     * @param mixed $args
     */
    public function add($alias, $service = '', $args = []);

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
     * Get all default services.
     *
     * @return array $services
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
     * Initialize custom components.
     *
     * @param string    $classname
     * @param array     $components
     */
    public function initComponents($classname, $components = []);

    /**
     * Initialize configs files containing theme definitions.
     *
     * @param array $args
     */
    public function initConfigs($args);
}
