<?php

namespace GetOlympus\Zeus\Posttype;

/**
 * Posttype interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface PosttypeInterface
{
    /**
     * Adds a new metabox.
     *
     * @param  string  $title
     * @param  array   $fields
     *
     * @throws PosttypeException
     */
    public function addMetabox($title, $fields) : void;

    /**
     * Return post types reserved slugs.
     *
     * @return array
     */
    public function getReservedSlugs() : array;
}
