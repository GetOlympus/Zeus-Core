<?php

namespace GetOlympus\Zeus\Widget\Model;

/**
 * Widget model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface WidgetModelInterface
{

    /**
     * Gets the value of classname.
     *
     * @return string
     */
    public function getClassname();

    /**
     * Sets the value of classname.
     *
     * @param string $classname the classname
     *
     * @return self
     */
    public function setClassname($classname);

    /**
     * Gets the value of displayTitle.
     *
     * @return boolean
     */
    public function getDisplayTitle();

    /**
     * Sets the value of displayTitle.
     *
     * @param boolean $displayTitle the displayTitle
     *
     * @return self
     */
    public function setDisplayTitle($displayTitle);

    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields();

    /**
     * Sets the value of fields.
     *
     * @param array $fields the fields
     *
     * @return self
     */
    public function setFields(array $fields);

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

    /**
     * Gets the The "height" key is never used. For more informations:.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Sets the The "height" key is never used. For more informations:.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Gets the value of settings.
     *
     * @return array
     */
    public function getSettings();

    /**
     * Sets the value of settings.
     *
     * @param array $settings the settings
     *
     * @return self
     */
    public function setSettings(array $settings);

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
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title);
}
