<?php

namespace GetOlympus\Zeus\Field\Model;

use GetOlympus\Zeus\Field\Model\FieldModelInterface;

/**
 * Field model.
 *
 * @package Olympus Zeus-Core
 * @subpackage Field\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class FieldModel implements FieldModelInterface
{
    /**
     * @var array
     */
    protected $contents;

    /**
     * @var array
     */
    protected $details;

    /**
     * @var string
     */
    protected $faIcon = 'fa-circle-o';

    /**
     * @var boolean
     */
    protected $hasId = true;

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
     * Gets the value of contents.
     *
     * @return array
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Sets the value of contents.
     *
     * @param array $contents the contents
     *
     * @return self
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

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
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Gets the value of faIcon.
     *
     * @return string
     */
    public function getFaIcon()
    {
        return $this->faIcon;
    }

    /**
     * Sets the value of faIcon.
     *
     * @param string $faIcon the fa icon
     *
     * @return self
     */
    public function setFaIcon($faIcon)
    {
        $this->faIcon = $faIcon;

        return $this;
    }

    /**
     * Gets the value of hasId.
     *
     * @return boolean
     */
    public function getHasId()
    {
        return $this->hasId;
    }

    /**
     * Sets the value of hasId.
     *
     * @param boolean $hasId the has id
     *
     * @return self
     */
    public function setHasId($hasId)
    {
        $this->hasId = $hasId;

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
     * @param string $script the script
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
     * @param string $style the style
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
     * @param string $template the template
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
     * @param array $vars the vars
     *
     * @return self
     */
    public function setVars(array $vars)
    {
        $name = isset($vars['name']) && !empty($vars['name']) ? $vars['name'] : $vars['id'];
        $vars['name'] = $name;

        $this->vars = $vars;

        return $this;
    }
}
