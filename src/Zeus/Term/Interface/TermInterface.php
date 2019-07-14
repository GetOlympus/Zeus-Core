<?php

namespace GetOlympus\Zeus\Term\Interface;

/**
 * Term interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Term\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface TermInterface
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
