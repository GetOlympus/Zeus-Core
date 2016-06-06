<?php

namespace GetOlympus\Hera\Field\Controller;

/**
 * Field interface.
 *
 * @package Olympus Hera
 * @subpackage Field\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface FieldInterface
{
    /**
     * Build Field component.
     *
     * @param string    $id
     * @param array     $contents
     * @param array     $details
     */
    public static function build($id, $contents = [], $details = []);

    /**
     * Gets the value of field.
     *
     * @return FieldModel
     */
    public function getField();

    /**
     * Define if field has an ID or not.
     *
     * @return boolean $hasId
     */
    public static function getHasId();

    /**
     * Gets the value of instance.
     *
     * @return Field
     */
    public static function getInstance();

    /**
     * Define if field is authorized or not.
     *
     * @return boolean $isAuthorized
     */
    public static function getIsAuthorized();

    /**
     * Retrieve field value
     *
     * @param array $details
     * @param object $default
     * @param string $id
     * @param boolean $multiple
     *
     * @return string|integer|array|object|boolean|null
     */
    public static function getValue($details, $default, $id = '', $multiple = false);

    /**
     * Render HTML component.
     *
     * @param array $details
     * @param boolean $renderView
     * @param string $context
     */
    public function render($details = [], $renderView = true, $context = 'field');
}
