<?php

namespace GetOlympus\Zeus\Field\Implementation;

/**
 * Field implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Field\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface FieldImplementation
{
    /**
     * Render assets' component.
     *
     * @return array
     */
    public function assets();

    /**
     * Build Field component.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @return Field
     */
    public static function build($identifier, $options = []);

    /**
     * Prepare HTML component for templating.
     *
     * @param  string  $template
     * @param  object  $object
     * @param  string  $type
     *
     * @return string
     */
    public function prepare($template = 'metabox', $object = null, $type = 'default');

    /**
     * Retrieve Field translations
     *
     * @return array
     */
    public static function translate();

    /**
     * Retrieve Field value
     *
     * @param  string  $identifier
     * @param  object  $object
     * @param  object  $default
     * @param  string  $type
     *
     * @return object
     */
    public static function value($identifier, $object, $default, $type = 'default');
}
