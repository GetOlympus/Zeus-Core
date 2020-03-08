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
     * @param  array   $settings
     *
     * @throws CustomizerException
     */
    public function addControl($identifier, $options, $settings = []) : void;

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
     *
     * @throws CustomizerException
     */
    public function addSection($identifier, $options) : void;
}
