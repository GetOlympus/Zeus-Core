<?php

namespace GetOlympus\Hera\AdminPage\Model;

use GetOlympus\Hera\AdminPage\Controller\AdminPageHook;

/**
 * AdminPage model interface.
 *
 * @package Olympus Hera
 * @subpackage AdminPage\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.7
 *
 */

interface AdminPageModelInterface
{
    /**
     * Gets the value of hook.
     *
     * @return AdminPageHook
     */
    public function getHook();

    /**
     * Sets the value of hook.
     *
     * @param AdminPageHook $hook the hook
     *
     * @return self
     */
    public function setHook(AdminPageHook $hook);

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Sets the value of identifier.
     *
     * @param string $identifier the identifier
     *
     * @return self
     */
    public function setIdentifier($identifier);

    /**
     * Adds a new value of pages.
     *
     * @param string    $identifier the identifier
     * @param array     $options    the options
     *
     * @return self
     */
    public function addPage($identifier, $options);

    /**
     * Check if the value is set.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function hasPage($identifier);

    /**
     * Sets the value of page.
     *
     * @param string $identifier the identifier
     * @param array  $options    the options
     *
     * @return self
     */
    public function updatePage($identifier, $options);

    /**
     * Gets the value of pages.
     *
     * @param   string $identifier the identifier
     * @return  array
     */
    public function getPages($identifier = '');

    /**
     * Sets the value of pages.
     *
     * @param array $pages the pages
     *
     * @return self
     */
    public function setPages($pages);
}
