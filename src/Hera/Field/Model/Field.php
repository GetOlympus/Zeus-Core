<?php

namespace GetOlympus\Hera\Field\Model;

/**
 * Abstract class to define Field model.
 *
 * @package Olympus Hera
 * @subpackage Field\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Field
{
    /**
     * @var string
     */
    protected $faIcon;

    /**
     * @var boolean
     */
    protected $hasId = true;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * @var boolean
     */
    protected $isAuthorized = true;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $vars;

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
     * Gets the value of includes.
     *
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Sets the value of includes.
     *
     * @param array $includes the includes
     *
     * @return self
     */
    public function setIncludes(array $includes)
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * Gets the value of isAuthorized.
     *
     * @return boolean
     */
    public function getIsAuthorized()
    {
        return $this->isAuthorized;
    }

    /**
     * Sets the value of isAuthorized.
     *
     * @param boolean $isAuthorized the is authorized
     *
     * @return self
     */
    public function setIsAuthorized($isAuthorized)
    {
        $this->isAuthorized = $isAuthorized;

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
        $this->vars = $vars;

        return $this;
    }
}
