<?php

namespace GetOlympus\Zeus\Posttype\Interface;

/**
 * Post type model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface PosttypeModelInterface
{
    /**
     * Gets the value of args.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Sets the value of args.
     *
     * @param  array   $args
     *
     * @return self
     */
    public function setArgs($args);

    /**
     * Gets the value of metabox.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getMetabox($identifier = '');

    /**
     * Sets the value of metabox.
     *
     * @param  string  $identifier
     * @param  string  $title
     * @param  array   $fields
     *
     * @return self
     */
    public function setMetabox($identifier, $title, $fields);

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Sets the value of slug.
     *
     * @param  string  $slug
     *
     * @return self
     */
    public function setSlug($slug);
}
