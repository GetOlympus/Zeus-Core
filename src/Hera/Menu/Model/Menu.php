<?php

namespace GetOlympus\Hera\Menu\Model;

/**
 * Abstract class to define Menu model.
 *
 * @package Olympus Hera
 * @subpackage Menu\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Menu
{
    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $pages = [];

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
     * Gets the value of a uniq option.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function getOption($identifier)
    {
        return isset($this->options[$identifier]) ? $this->options[$identifier] : null;
    }

    /**
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Check if the value is set.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function hasOption($identifier)
    {
        return isset($this->options[$identifier]);
    }

    /**
     * Sets the value of options.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Adds a new value of pages.
     *
     * @param string $identifier the identifier
     * @param array $contents the contents
     *
     * @return self
     */
    public function addPage($identifier, $contents)
    {
        $this->pages[$identifier] = $contents;

        return $this;
    }

    /**
     * Gets the value of a uniq page.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function getPage($identifier)
    {
        return isset($this->pages[$identifier]) ? $this->pages[$identifier] : null;
    }

    /**
     * Gets the value of pages.
     *
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
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
