<?php

namespace GetOlympus\Hera\AdminPage\Controller;

/**
 * AdminPage interface.
 *
 * @package Olympus Hera
 * @subpackage AdminPage\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.7
 *
 */

interface AdminPageInterface
{
    /**
     * Build AdminPageModel and initialize admin pages.
     */
    public function init();

    /**
     * Add root admin page.
     */
    public function addRootPage();

    /**
     * Add root admin bar page.
     */
    public function addRootAdminBar();

    /**
     * Add child admin page.
     *
     * @param string    $slug
     * @param array     $options
     */
    public function addChild($slug, $options);

    /**
     * Add child admin bar page.
     *
     * @param string    $slug
     * @param array     $options
     */
    public function addChildAdminBar($slug, $options);

    /**
     * Hook callback.
     */
    public function callback();
}
