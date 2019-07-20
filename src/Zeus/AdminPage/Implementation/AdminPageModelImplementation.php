<?php

namespace GetOlympus\Zeus\AdminPage\Implementation;

/**
 * AdminPage model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

interface AdminPageModelImplementation
{
    /**
     * Gets the value of adminbar.
     *
     * @return string
     */
    public function getAdminbar();

    /**
     * Sets the value of adminbar.
     *
     * @param  string  $adminbar
     *
     * @return self
     */
    public function setAdminbar($adminbar);

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Sets the value of identifier.
     *
     * @param  string  $identifier
     *
     * @return self
     */
    public function setIdentifier($identifier);

    /**
     * Gets the value of pages.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getPages($identifier = '');

    /**
     * Sets the value of pages.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @return self
     */
    public function setPages($identifier, $options);

    /**
     * Gets the value of parent.
     *
     * @return string
     */
    public function getParent();

    /**
     * Sets the value of parent.
     *
     * @param  string  $parent
     * @param  array   $available
     *
     * @return self
     */
    public function setParent($parent = '', $available = []);
}
