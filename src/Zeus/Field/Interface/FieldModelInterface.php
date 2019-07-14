<?php

namespace GetOlympus\Zeus\Field\Interface;

/**
 * Field model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Field\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface FieldModelInterface
{
    /**
     * Gets the value of adminscripts.
     *
     * @return array
     */
    public function getAdminscripts();

    /**
     * Sets the value of adminscripts.
     *
     * @param  array   $adminscripts
     *
     * @return self
     */
    public function setAdminscripts($adminscripts);

    /**
     * Gets the value of adminstyles.
     *
     * @return array
     */
    public function getAdminstyles();

    /**
     * Sets the value of adminstyles.
     *
     * @param  array   $adminstyles
     *
     * @return self
     */
    public function setAdminstyles($adminstyles);

    /**
     * Gets the value of defaults.
     *
     * @return array
     */
    public function getDefaults();

    /**
     * Sets the value of defaults.
     *
     * @param  array   $defaults
     *
     * @return self
     */
    public function setDefaults($defaults);

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
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Sets the value of options.
     *
     * @param  array   $options
     *
     * @return self
     */
    public function setOptions($options);

    /**
     * Gets the value of script.
     *
     * @return string
     */
    public function getScript();

    /**
     * Sets the value of script.
     *
     * @param  string  $script
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
     * @param  string  $style
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
     * @param  string  $template
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
     * @param  array   $vars
     *
     * @return self
     */
    public function setVars($vars);
}
