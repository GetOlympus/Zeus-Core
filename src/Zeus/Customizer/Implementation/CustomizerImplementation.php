<?php

namespace GetOlympus\Zeus\Customizer\Implementation;

/**
 * Customizer implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

interface CustomizerImplementation
{
    /**
     * Adds a new value of control.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  array   $settings
     */
    public function addControl($identifier, $options, $settings = []);

    /**
     * Adds a new value of panel.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $page_redirect
     */
    public function addPanel($identifier, $options, $page_redirect = '');

    /**
     * Adds a new value of section.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function addSection($identifier, $options);

    /**
     * Return admin scripts.
     *
     * @return array
     */
    public function getAdminscripts();

    /**
     * Return available mime types.
     *
     * @return array
     */
    public function getAvailableMimetypes();

    /**
     * Return available types.
     *
     * @param  string  $type
     *
     * @return array
     */
    public function getAvailableTypes($type = '');

    /**
     * Return default templates.
     *
     * @return array
     */
    public function getDefaultTemplates();

    /**
     * Return scripts.
     *
     * @return array
     */
    public function getScripts();

    /**
     * Register customizer.
     */
    public function register();
}
