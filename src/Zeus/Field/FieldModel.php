<?php

namespace GetOlympus\Zeus\Field;

/**
 * Field model.
 *
 * @package    OlympusZeusCore
 * @subpackage Field
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class FieldModel
{
    /**
     * @var array
     */
    protected $adminscripts = [];

    /**
     * @var array
     */
    protected $adminstyles = [];

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $script = '';

    /**
     * @var string
     */
    protected $style = '';

    /**
     * @var string
     */
    protected $template = 'field.html.twig';

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * Gets the value of adminscripts.
     *
     * @return array
     */
    public function getAdminscripts() : array
    {
        return $this->adminscripts;
    }

    /**
     * Sets the value of adminscripts.
     *
     * @param  array   $adminscripts
     */
    public function setAdminscripts($adminscripts) : void
    {
        $this->adminscripts = $adminscripts;
    }

    /**
     * Gets the value of adminstyles.
     *
     * @return array
     */
    public function getAdminstyles() : array
    {
        return $this->adminstyles;
    }

    /**
     * Sets the value of adminstyles.
     *
     * @param  array   $adminstyles
     */
    public function setAdminstyles($adminstyles) : void
    {
        $this->adminstyles = $adminstyles;
    }

    /**
     * Gets the value of defaults.
     *
     * @return array
     */
    public function getDefaults() : array
    {
        return $this->defaults;
    }

    /**
     * Sets the value of defaults.
     *
     * @param  array   $defaults
     */
    public function setDefaults($defaults) : void
    {
        $this->defaults = $defaults;
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
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param  array   $options
     */
    public function setOptions($options) : void
    {
        $this->options = $options;
    }

    /**
     * Gets the value of script.
     *
     * @return string
     */
    public function getScript() : string
    {
        return $this->script;
    }

    /**
     * Sets the value of script.
     *
     * @param  string  $script
     */
    public function setScript($script) : void
    {
        $this->script = $script;
    }

    /**
     * Gets the value of style.
     *
     * @return string
     */
    public function getStyle() : string
    {
        return $this->style;
    }

    /**
     * Sets the value of style.
     *
     * @param  string  $style
     */
    public function setStyle($style) : void
    {
        $this->style = $style;
    }

    /**
     * Gets the value of template.
     *
     * @return string
     */
    public function getTemplate() : string
    {
        return $this->template;
    }

    /**
     * Sets the value of template.
     *
     * @param  string  $template
     */
    public function setTemplate($template) : void
    {
        $this->template = $template;
    }

    /**
     * Gets the value of vars.
     *
     * @return array
     */
    public function getVars() : array
    {
        return $this->vars;
    }

    /**
     * Sets the value of vars.
     *
     * @param  array   $vars
     */
    public function setVars($vars) : void
    {
        $vars['name'] = isset($vars['name']) && !empty($vars['name']) ? $vars['name'] : $this->getIdentifier();

        $this->vars = $vars;
    }
}
