<?php

namespace GetOlympus\Zeus\Field;

use GetOlympus\Zeus\Field\Field;

/**
 * Field interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Field
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface FieldInterface
{
    /**
     * Render assets' component.
     *
     * @return array
     */
    public function assets() : array;

    /**
     * Build Field component.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  bool    $useid
     *
     * @throws FieldException
     *
     * @return Field
     */
    public static function build($identifier, $options = []) : Field;

    /**
     * Prepare HTML component for templating.
     *
     * @param  string  $template
     * @param  object  $object
     * @param  string  $type
     *
     * @return array
     */
    public function prepare($template = 'metabox', $object = null, $type = 'default') : array;

    /**
     * Retrieve Field translations
     *
     * @throws FieldException
     *
     * @return array
     */
    public static function translate() : array;

    /**
     * Retrieve Field value
     *
     * @param  string  $identifier
     * @param  object  $object
     * @param  object  $default
     * @param  string  $type
     *
     * @return mixed
     */
    public static function value($identifier, $object, $default, $type = 'default');
}
