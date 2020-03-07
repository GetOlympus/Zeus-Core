<?php

namespace GetOlympus\Zeus\AdminPage;

/**
 * AdminPage model.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

class AdminPageModel
{
    /**
     * @var bool
     */
    protected $adminbar = false;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @var string
     */
    protected $parent = '';

    /**
     * Gets the value of adminbar.
     *
     * @return bool
     */
    public function getAdminbar() : bool
    {
        return $this->adminbar;
    }

    /**
     * Sets the value of adminbar.
     *
     * @param  string  $adminbar
     */
    public function setAdminbar($adminbar) : void
    {
        $this->adminbar = $adminbar;
    }

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    /**
     * Sets the value of identifier.
     *
     * @param  string  $identifier
     */
    public function setIdentifier($identifier) : void
    {
        $this->identifier = $identifier;
    }

    /**
     * Gets the value of pages.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getPages($identifier = '') : array
    {
        if (!empty($identifier)) {
            return isset($this->pages[$identifier]) ? $this->pages[$identifier] : [];
        }

        return $this->pages;
    }

    /**
     * Sets the value of pages.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function setPages($identifier, $options) : void
    {
        $this->pages[$identifier] = $options;
    }

    /**
     * Gets the value of parent.
     *
     * @return string
     */
    public function getParent() : string
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param  string  $parent
     * @param  array   $available
     */
    public function setParent($parent = '', $available = []) : void
    {
        $this->parent = !empty($parent) && array_key_exists($parent, $available) ? $parent : '';
    }
}
