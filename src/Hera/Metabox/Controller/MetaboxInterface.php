<?php

namespace GetOlympus\Hera\Metabox\Controller;

/**
 * Metabox interface.
 *
 * @package Olympus Hera
 * @subpackage Metabox\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface MetaboxInterface
{
    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $slug
     * @param string $title
     * @param array $args
     */
    public function init($identifier, $slug, $title, $args);

    /**
     * Add metabox.
     */
    public function addMetabox();

    /**
     * Callback function.
     *
     * @param array $post
     * @param array $args
     * @return int|null
     */
    public function callback($post, $args);
}
