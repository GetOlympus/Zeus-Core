<?php

namespace GetOlympus\Zeus\Widget;

/**
 * Widget model.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class WidgetModel
{
    /**
     * @var string
     */
    protected $classname = '';

    /**
     * @var bool
     */
    protected $displayTitle = true;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * The "height" key is never used. For more informations:
     * @see https://core.trac.wordpress.org/browser/tags/4.5.2/src/wp-includes/widgets.php#L490
     *
     * @var array
     */
    protected $options = [
        'height' => 200,
        'width'  => 250,
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * Gets the value of classname.
     *
     * @return string
     */
    public function getClassname() : string
    {
        return $this->classname;
    }

    /**
     * Sets the value of classname.
     *
     * @param  string  $classname
     */
    public function setClassname($classname) : void
    {
        $this->classname = $classname;
    }

    /**
     * Gets the value of displayTitle.
     *
     * @return bool
     */
    public function getDisplayTitle() : bool
    {
        return $this->displayTitle;
    }

    /**
     * Sets the value of displayTitle.
     *
     * @param  bool    $displayTitle
     */
    public function setDisplayTitle($displayTitle) : void
    {
        $this->displayTitle = $displayTitle;
    }

    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * Sets the value of fields.
     *
     * @param  array   $fields
     */
    public function setFields(array $fields) : void
    {
        $this->fields = $fields;
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
     * Gets the The "height" key is never used. For more informations:.
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Sets the The "height" key is never used. For more informations:.
     *
     * @param  array   $options
     */
    public function setOptions(array $options) : void
    {
        $this->options = $options;
    }

    /**
     * Gets the value of settings.
     *
     * @return array
     */
    public function getSettings() : array
    {
        return $this->settings;
    }

    /**
     * Sets the value of settings.
     *
     * @param  array   $settings
     */
    public function setSettings(array $settings) : void
    {
        $this->settings = $settings;
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
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param  string  $title
     */
    public function setTitle($title) : void
    {
        $this->title = $title;
    }
}
