<?php

namespace GetOlympus\Zeus\Customizer\Implementation;

/**
 * Customizer hook implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

interface CustomizerHookImplementation
{
    /**
     * Customize and manipulate the Theme Customization admin screen.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
     *
     * @param  object  $wp_customize
     */
    public function customizeRegister($wp_customize);

    /**
     * Displays controls.
     *
     * @param  object  $wp_customize
     * @param  array   $controls
     */
    public function displayControls($wp_customize, $controls);

    /**
     * Displays panels.
     *
     * @param  object  $wp_customize
     * @param  array   $panels
     */
    public function displayPanels($wp_customize, $panels);

    /**
     * Displays sections.
     *
     * @param  object  $wp_customize
     * @param  array   $sections
     */
    public function displaySections($wp_customize, $sections);
}
