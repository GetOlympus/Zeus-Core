<?php

namespace GetOlympus\Hera\Widget\Model;

/**
 * Widget model interface.
 *
 * @package Olympus Hera
 * @subpackage Widget\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.3
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
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Sets the value of options.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions(array $options);
}