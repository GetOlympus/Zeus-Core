<?php

namespace GetOlympus\Hera\Term\Controller;

/**
 * Term interface.
 *
 * @package Olympus Hera
 * @subpackage Term\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface TermInterface
{
    /**
     * Initialization.
     *
     * @param string $slug
     * @param array $args
     * @param array $labels
     */
    public function init($slug, $posttype, $args, $labels);

    /**
     * Build args.
     *
     * @param string $slug
     * @return array $args
     */
    public function defaultArgs($slug);

    /**
     * Build labels.
     *
     * @param string $plural
     * @param string $singular
     * @param boolean $hierarchical
     * @return array $labels
     */
    public function defaultLabels($plural, $singular, $hierarchical = true);

    /**
     * Register post types.
     */
    public function register();
}
