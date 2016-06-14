<?php

namespace GetOlympus\Hera\Template\Model;

/**
 * Template model interface.
 *
 * @package Olympus Hera
 * @subpackage Template\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
 *
 */

interface TemplateModelInterface
{
    /**
     * Gets the value of currentPage.
     *
     * @return string
     */
    public function getCurrentPage();

    /**
     * Sets the value of currentPage.
     *
     * @param string $currentPage the current page
     *
     * @return self
     */
    public function setCurrentPage($currentPage);

    /**
     * Gets the value of currentSection.
     *
     * @return string
     */
    public function getCurrentSection();

    /**
     * Sets the value of currentSection.
     *
     * @param string $currentSection the current section
     *
     * @return self
     */
    public function setCurrentSection($currentSection);

    /**
     * Gets the value of details.
     *
     * @return array
     */
    public function getDetails();

    /**
     * Sets the value of details.
     *
     * @param array $details the details
     *
     * @return self
     */
    public function setDetails(array $details);

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
}
