<?php

namespace GetOlympus\Zeus\AdminPage\Model;

use GetOlympus\Zeus\AdminPage\Controller\AdminPageHook;
use GetOlympus\Zeus\AdminPage\Model\AdminPageModelInterface;

/**
 * AdminPage model.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

class AdminPageModel implements AdminPageModelInterface
{
    /**
     * @var AdminPageHook
     */
    protected $hook;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * Gets the value of hook.
     *
     * @return AdminPageHook
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Sets the value of hook.
     *
     * @param AdminPageHook $hook the hook
     *
     * @return self
     */
    public function setHook(AdminPageHook $hook)
    {
        $this->hook = $hook;

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
     * Adds a new value of pages.
     *
     * @param string    $identifier the identifier
     * @param array     $options    the options
     *
     * @return self
     */
    public function addPage($identifier, $options)
    {
        $this->pages[$identifier] = $options;

        return $this;
    }

    /**
     * Check if the value is set.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function hasPage($identifier)
    {
        return isset($this->pages[$identifier]);
    }

    /**
     * Sets the value of page.
     *
     * @param string $identifier the identifier
     * @param array  $configs    the options configuration
     *
     * @return self
     */
    public function updatePage($identifier, $configs)
    {
        $this->pages[$identifier] = $configs;

        return $this;
    }

    /**
     * Gets the value of pages.
     *
     * @param   string $identifier the identifier
     * @return  array
     */
    public function getPages($identifier = '')
    {
        if (!empty($identifier)) {
            return $this->hasPage($identifier) ? $this->pages[$identifier] : null;
        }

        return $this->pages;
    }

    /**
     * Sets the value of pages.
     *
     * @param array $pages the pages
     *
     * @return self
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }
}
