<?php

namespace GetOlympus\Hera\Menu\Controller;

/**
 * Menu interface.
 *
 * @package Olympus Hera
 * @subpackage Menu\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface MenuInterface
{
    /**
     * Add root single menu.
     *
     * @param string $identifier
     * @param array $options
     */
    public function addRootMenu($identifier, $options);

    /**
     * Add child single menu.
     *
     * @param string $slug
     * @param array $options
     * @param string $wpidentifier
     */
    public function addChild($slug, $options, $wpidentifier = '');

    /**
     * Add root admin bar menu.
     */
    public function addRootAdminBar();

    /**
     * Define hook.
     *
     * @param string $slug
     * @param array $options
     * @param string $wpidentifier
     */
    public function addChildAdminBar($slug, $options, $wpidentifier = '');

    /**
     * Hook method.
     */
    public function callback();
}
