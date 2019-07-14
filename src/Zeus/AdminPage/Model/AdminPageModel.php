<?php

namespace GetOlympus\Zeus\AdminPage\Model;

use GetOlympus\Zeus\AdminPage\Implementation\AdminPageModelImplementation;

/**
 * AdminPage model.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

class AdminPageModel implements AdminPageModelImplementation
{
    /**
     * @var boolean
     */
    protected $adminbar;

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
     * @return string
     */
    public function getAdminbar()
    {
        return $this->adminbar;
    }

    /**
     * Sets the value of adminbar.
     *
     * @param string $adminbar the adminbar
     *
     * @return self
     */
    public function setAdminbar($adminbar)
    {
        $this->adminbar = $adminbar;

        return $this;
    }

    /**
     * Gets the value of identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the value of identifier.
     *
     * @param string $identifier the identifier
     *
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Gets the value of pages.
     *
     * @param   string    $identifier
     * @return  array
     */
    public function getPages($identifier = '')
    {
        if (!empty($identifier)) {
            return isset($this->pages[$identifier]) ? $this->pages[$identifier] : [];
        }

        return $this->pages;
    }

    /**
     * Sets the value of pages.
     *
     * @param string    $identifier the identifier
     * @param array     $options    the options
     *
     * @return self
     */
    public function setPages($identifier, $options)
    {
        $this->pages[$identifier] = $options;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param string $parent
     * @param array  $available
     *
     * @return self
     */
    public function setParent($parent = '', $available = [])
    {
        $this->parent = !empty($parent) && array_key_exists($parent, $available) ? $parent : '';

        return $this;
    }
}
