<?php

namespace GetOlympus\Zeus\Render\Implementation;

/**
 * Render implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Render\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface RenderImplementation
{
    /**
     * Add WordPress and Custom functions
     */
    public function addFunctions();

    /**
     * Enqueue scripts and styles.
     *
     * @param  array   $assets
     */
    public function enqueue($assets = []);

    /**
     * Render TWIG component.
     */
    public function view();
}
