<?php

namespace GetOlympus\Hera\Template\Model;

/**
 * Template model.
 *
 * @package Olympus Hera
 * @subpackage Template\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class TemplateModel
{
    /**
     * @var string
     */
    protected $currentPage = '';

    /**
     * @var string
     */
    protected $currentSection = '';

    /**
     * @var array
     */
    protected $details = [];

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Gets the value of currentPage.
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Sets the value of currentPage.
     *
     * @param string $currentPage the current page
     *
     * @return self
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Gets the value of currentSection.
     *
     * @return string
     */
    public function getCurrentSection()
    {
        return $this->currentSection;
    }

    /**
     * Sets the value of currentSection.
     *
     * @param string $currentSection the current section
     *
     * @return self
     */
    public function setCurrentSection($currentSection)
    {
        $this->currentSection = $currentSection;

        return $this;
    }

    /**
     * Gets the value of details.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Sets the value of details.
     *
     * @param array $details the details
     *
     * @return self
     */
    public function setDetails(array $details)
    {
        if (preg_match('/<span style=\"color\:\#([a-zA-Z0-9]{3,6})\">(.*)<\/span>/i', $details['title'], $matches)) {
            $details['title'] = '<b style="color:#'.$matches[1].'">'.$matches[2].'</b>';
        }

        $this->details = $details;

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
}
