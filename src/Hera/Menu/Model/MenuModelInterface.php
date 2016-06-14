<?php

namespace GetOlympus\Hera\Menu\Model;

/**
 * Menu model interface.
 *
 * @package Olympus Hera
 * @subpackage Menu\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
 *
 */

interface MenuModelInterface
{
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
     * Gets the value of a uniq option.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function getOption($identifier);

    /**
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Check if the value is set.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function hasOption($identifier);

    /**
     * Sets the value of options.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions($options);

    /**
     * Adds a new value of pages.
     *
     * @param string $identifier the identifier
     * @param array $contents the contents
     *
     * @return self
     */
    public function addPage($identifier, $contents);

    /**
     * Gets the value of a uniq page.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function getPage($identifier);

    /**
     * Gets the value of pages.
     *
     * @return array
     */
    public function getPages();

    /**
     * Check if the value is set.
     *
     * @param string $identifier the identifier
     *
     * @return array
     */
    public function hasPage($identifier);

    /**
     * Sets the value of pages.
     *
     * @param array $pages the pages
     *
     * @return self
     */
    public function setPages($pages);
}
