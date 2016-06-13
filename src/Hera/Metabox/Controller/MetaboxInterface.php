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
     * Build Metabox component.
     *
     * @param string    $title
     * @param array     $fields
     */
    public static function build($title, $fields = []);

    /**
     * Gets the value of instance.
     *
     * @return Metabox
     */
    public static function getInstance();

    /**
     * Gets the value of metabox.
     *
     * @return MetaboxModel
     */
    public function getMetabox();

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
