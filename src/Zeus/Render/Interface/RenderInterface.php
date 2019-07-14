<?php

namespace GetOlympus\Zeus\Render\Interface;

/**
 * Render interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Render\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface RenderInterface
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
