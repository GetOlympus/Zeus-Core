<?php

namespace GetOlympus\Zeus\Metabox;

use GetOlympus\Zeus\Metabox\Metabox;

/**
 * Metabox interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface MetaboxInterface
{
    /**
     * Build Metabox component.
     *
     * @param  string  $title
     * @param  array   $fields
     *
     * @throws MetaboxException
     *
     * @return Metabox
     */
    public static function build($title, $fields = []) : Metabox;

    /**
     * Initialization.
     *
     * @param  string  $identifier
     * @param  string  $slug
     */
    public function init($identifier, $slug) : void;

    /**
     * Add metabox.
     */
    public function addMetabox() : void;

    /**
     * Callback function.
     *
     * @param  array   $post
     * @param  array   $args
     *
     * @throws MetaboxException
     *
     * @return int|null
     */
    public function callback($post, $args) : ?int;
}
