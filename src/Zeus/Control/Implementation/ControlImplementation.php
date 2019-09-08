<?php

namespace GetOlympus\Zeus\Control\Implementation;

/**
 * Control implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Control\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.2
 *
 */

interface ControlImplementation
{
    /**
     * Enqueue scripts and styles.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function copyFile($path);

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue();

    /**
     * Render Control in the customizer.
     */
    public function render_content();

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     */
    public function to_json();

    /**
     * Retrieve Control translations
     *
     * @return array
     */
    public static function translate();
}
