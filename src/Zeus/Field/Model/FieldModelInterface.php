<?php

namespace GetOlympus\Zeus\Field\Model;

/**
 * Field model interface.
 *
 * @package Olympus Zeus-Core
 * @subpackage Field\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
 *
 */

interface FieldModelInterface
{
    /**
     * Gets the value of contents.
     *
     * @return array
     */
    public function getContents();

    /**
     * Sets the value of contents.
     *
     * @param array $contents the contents
     *
     * @return self
     */
    public function setContents($contents);

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
    public function setDetails($details);

    /**
     * Gets the value of faIcon.
     *
     * @return string
     */
    public function getFaIcon();

    /**
     * Sets the value of faIcon.
     *
     * @param string $faIcon the fa icon
     *
     * @return self
     */
    public function setFaIcon($faIcon);

    /**
     * Gets the value of hasId.
     *
     * @return boolean
     */
    public function getHasId();

    /**
     * Sets the value of hasId.
     *
     * @param boolean $hasId the has id
     *
     * @return self
     */
    public function setHasId($hasId);

    /**
     * Gets the value of script.
     *
     * @return string
     */
    public function getScript();

    /**
     * Sets the value of script.
     *
     * @param string $script the script
     *
     * @return self
     */
    public function setScript($script);

    /**
     * Gets the value of style.
     *
     * @return string
     */
    public function getStyle();

    /**
     * Sets the value of style.
     *
     * @param string $style the style
     *
     * @return self
     */
    public function setStyle($style);

    /**
     * Gets the value of template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Sets the value of template.
     *
     * @param string $template the template
     *
     * @return self
     */
    public function setTemplate($template);

    /**
     * Gets the value of vars.
     *
     * @return array
     */
    public function getVars();

    /**
     * Sets the value of vars.
     *
     * @param array $vars the vars
     *
     * @return self
     */
    public function setVars(array $vars);
}
