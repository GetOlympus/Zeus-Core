<?php

namespace GetOlympus\Zeus\Field\Model;

use GetOlympus\Zeus\Field\Implementation\FieldModelImplementation;

/**
 * Field model.
 *
 * @package    OlympusZeusCore
 * @subpackage Field\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class FieldModel implements FieldModelImplementation
{
    /**
     * @var array
     */
    protected $adminscripts;

    /**
     * @var array
     */
    protected $adminstyles;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var array
     */
    protected $options;

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
    protected $vars;

    /**
     * Gets the value of adminscripts.
     *
     * @return array
     */
    public function getAdminscripts()
    {
        return $this->adminscripts;
    }

    /**
     * Sets the value of adminscripts.
     *
     * @param  array   $adminscripts
     *
     * @return self
     */
    public function setAdminscripts($adminscripts)
    {
        $this->adminscripts = $adminscripts;

        return $this;
    }

    /**
     * Gets the value of adminstyles.
     *
     * @return array
     */
    public function getAdminstyles()
    {
        return $this->adminstyles;
    }

    /**
     * Sets the value of adminstyles.
     *
     * @param  array   $adminstyles
     *
     * @return self
     */
    public function setAdminstyles($adminstyles)
    {
        $this->adminstyles = $adminstyles;

        return $this;
    }

    /**
     * Gets the value of defaults.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Sets the value of defaults.
     *
     * @param  array   $defaults
     *
     * @return self
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;

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
     * @param  string  $identifier
     *
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
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
     * Sets the value of options.
     *
     * @param  array   $options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of script.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Sets the value of script.
     *
     * @param  string  $script
     *
     * @return self
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Gets the value of style.
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Sets the value of style.
     *
     * @param  string  $style
     *
     * @return self
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Gets the value of template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the value of template.
     *
     * @param  string  $template
     *
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Gets the value of vars.
     *
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Sets the value of vars.
     *
     * @param  array   $vars
     *
     * @return self
     */
    public function setVars($vars)
    {
        $vars['name'] = isset($vars['name']) && !empty($vars['name']) ? $vars['name'] : $this->getIdentifier();

        $this->vars = $vars;

        return $this;
    }
}
