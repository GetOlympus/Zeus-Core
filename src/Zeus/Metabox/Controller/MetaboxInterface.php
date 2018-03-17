<?php

namespace GetOlympus\Zeus\Metabox\Controller;

/**
 * Metabox interface.
 *
 * @package Olympus Zeus-Core
 * @subpackage Metabox\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface MetaboxInterface
{
    /**
     * Build Metabox component.
     *
     * @param string    $title
     * @param array     $fields
     */
    public static function build($title, $fields = []);

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $slug
     */
    public function init($identifier, $slug);

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
