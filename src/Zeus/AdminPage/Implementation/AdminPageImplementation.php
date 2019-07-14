<?php

namespace GetOlympus\Zeus\AdminPage\Implementation;

/**
 * AdminPage implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

interface AdminPageImplementation
{
    /**
     * Add admin bar page.
     *
     * @param  string  $barid
     * @param  string  $title
     * @param  string  $parent
     * @param  string  $url
     */
    public function addAdminBar($barid, $title, $parent = '', $url = '');

    /**
     * Adds a new value of pages.
     *
     * @param  string  $pageid
     * @param  array   $options
     */
    public function addPage($pageid, $options);

    /**
     * Add child page.
     *
     * @param  string  $pageid
     * @param  array   $options
     * @param  string  $parent
     */
    public function addPageChild($pageid, $options, $parent);

    /**
     * Add root page.
     *
     * @param  string  $pageid
     * @param  array   $options
     * @param  string  $parent
     */
    public function addPageRoot($pageid, $options, $parent = '');

    /**
     * Adds a new value of section.
     *
     * @param  string  $sectionid
     * @param  string  $pageid
     * @param  array   $options
     */
    public function addSection($sectionid, $pageid, $options);

    /**
     * Hook callback.
     */
    public function callback();

    /**
     * Get function to call from parent
     *
     * @param  string  $parent
     * @param  string  $function
     */
    public function functionFromParent($parent);
}
