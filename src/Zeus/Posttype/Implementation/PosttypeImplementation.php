<?php

namespace GetOlympus\Zeus\Posttype\Implementation;

/**
 * Posttype implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface PosttypeImplementation
{
    /**
     * Adds a new metabox.
     *
     * @param  string  $title
     * @param  array   $fields
     */
    public function addMetabox($title, $fields);

    /**
     * Return post types reserved slugs.
     *
     * @return array
     */
    public function getReservedSlugs();

    /**
     * Build PosttypeModel and initialize hook.
     */
    public function init();

    /**
     * Build args.
     *
     * @return array
     */
    public function defaultArgs();

    /**
     * Build labels.
     *
     * @param  string  $plural
     * @param  string  $singular
     *
     * @return array
     */
    public function defaultLabels($plural, $singular);

    /**
     * Build statuses.
     *
     * @param  string  $name
     *
     * @return array
     */
    public function defaultStatuses($name);

    /**
     * Register post types.
     */
    public function register();
}
