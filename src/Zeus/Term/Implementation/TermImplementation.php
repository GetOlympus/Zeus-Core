<?php

namespace GetOlympus\Zeus\Term\Implementation;

/**
 * Term implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Term\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface TermImplementation
{
    /**
     * Build TermModel and initialize hook.
     */
    public function init();

    /**
     * Build args.
     *
     * @return array   $args
     */
    public function defaultArgs();

    /**
     * Build labels.
     *
     * @param  string  $plural
     * @param  string  $singular
     * @param  boolean $hierarchical
     * @return array   $labels
     */
    public function defaultLabels($plural, $singular, $hierarchical = true);

    /**
     * Register post types.
     */
    public function register();
}
