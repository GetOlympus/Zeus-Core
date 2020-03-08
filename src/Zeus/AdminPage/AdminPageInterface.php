<?php

namespace GetOlympus\Zeus\AdminPage;

/**
 * AdminPage interface.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

interface AdminPageInterface
{
    /**
     * Adds a new value of pages.
     *
     * @param  string  $pageid
     * @param  array   $options
     *
     * @throws AdminPageException
     */
    public function addPage($pageid, $options) : void;

    /**
     * Adds a new value of section.
     *
     * @param  string  $sectionid
     * @param  string  $pageid
     * @param  array   $options
     *
     * @throws AdminPageException
     */
    public function addSection($sectionid, $pageid, $options) : void;
}
