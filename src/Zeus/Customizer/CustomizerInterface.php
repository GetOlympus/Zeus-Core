<?php

namespace GetOlympus\Zeus\Customizer;

/**
 * Customizer interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

interface CustomizerInterface
{
    /**
     * Adds a new value of control.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $classname
     *
     * @throws CustomizerException
     */
    public function addControl($identifier, $options, $classname = '') : void;

    /**
     * Register a new custom control/section type.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $path
     *
     * @throws CustomizerException
     */
    public function addCustomType($name, $type = 'control', $path = '') : void;

    /**
     * Adds a new value of panel.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $page_redirect
     *
     * @throws CustomizerException
     */
    public function addPanel($identifier, $options, $page_redirect = '') : void;

    /**
     * Adds a new value of section.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $classname
     *
     * @throws CustomizerException
     */
    public function addSection($identifier, $options, $classname = '') : void;

    /**
     * Adds a new value of setting.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @throws CustomizerException
     */
    public function addSetting($identifier, $options) : void;
}
