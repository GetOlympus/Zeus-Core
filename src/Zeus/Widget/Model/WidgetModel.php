<?php

namespace GetOlympus\Zeus\Widget\Model;

use GetOlympus\Zeus\Widget\Model\WidgetModelInterface;

/**
 * Widget model.
 *
 * @package Olympus Zeus-Core
 * @subpackage Widget\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class WidgetModel implements WidgetModelInterface
{
    /**
     * @var string
     */
    protected $classname;

    /**
     * @var boolean
     */
    protected $displayTitle = true;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * The "height" key is never used. For more informations:
     * @see https://core.trac.wordpress.org/browser/tags/4.5.2/src/wp-includes/widgets.php#L490
     *
     * @var array
     */
    protected $options = [
        'height'    => 200,
        'width'     => 250,
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $title;

    /**
     * Gets the value of classname.
     *
     * @return string
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * Sets the value of classname.
     *
     * @param string $classname the classname
     *
     * @return self
     */
    public function setClassname($classname)
    {
        $this->classname = $classname;

        return $this;
    }

    /**
     * Gets the value of displayTitle.
     *
     * @return boolean
     */
    public function getDisplayTitle()
    {
        return $this->displayTitle;
    }

    /**
     * Sets the value of displayTitle.
     *
     * @param boolean $displayTitle the displayTitle
     *
     * @return self
     */
    public function setDisplayTitle($displayTitle)
    {
        $this->displayTitle = $displayTitle;

        return $this;
    }

    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sets the value of fields.
     *
     * @param array $fields the fields
     *
     * @return self
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

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

    /**
     * Gets the The "height" key is never used. For more informations:.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the The "height" key is never used. For more informations:.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of settings.
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the value of settings.
     *
     * @param array $settings the settings
     *
     * @return self
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

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
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}