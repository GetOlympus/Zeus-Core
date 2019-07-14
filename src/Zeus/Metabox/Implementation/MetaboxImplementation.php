<?php

namespace GetOlympus\Zeus\Metabox\Implementation;

/**
 * Metabox implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface MetaboxImplementation
{
    /**
     * Build Metabox component.
     *
     * @param  string  $title
     * @param  array   $fields
     */
    public static function build($title, $fields = []);

    /**
     * Initialization.
     *
     * @param  string  $identifier
     * @param  string  $slug
     */
    public function init($identifier, $slug);

    /**
     * Add metabox.
     */
    public function addMetabox();

    /**
     * Callback function.
     *
     * @param  array   $post
     * @param  array   $args
     * @return int|null
     */
    public function callback($post, $args);
}
