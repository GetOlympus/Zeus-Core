<?php

namespace GetOlympus\Zeus\Posttype\Interface;

/**
 * Posttype interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Interface
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
     */
    public function addMetabox($title, $fields);

    /**
     * Return post types reserved slugs.
     *
     * @param  array   $reserved_slugs
     */
    public function getReservedSlugs();

    /**
     * Build PosttypeModel and initialize hook.
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
     * @return array   $labels
     */
    public function defaultLabels($plural, $singular);

    /**
     * Build statuses.
     *
     * @param  string  $name
     * @return array   $statuses
     */
    public function defaultStatuses($name);

    /**
     * Register post types.
     */
    public function register();
}
