<?php

namespace GetOlympus\Zeus\Field;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Field\FieldException;
use GetOlympus\Zeus\Field\FieldInterface;
use GetOlympus\Zeus\Field\FieldModel;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Abstract class to define all Field context with authorized fields, how to
 * write some functions and every usefull checks.
 *
 * @package    OlympusZeusCore
 * @subpackage Field
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Field extends Base implements FieldInterface
{
    /**
     * @var array
     */
    protected $adminscripts = [];

    /**
     * @var array
     */
    protected $adminstyles = [];

    /**
     * @var array
     */
    protected $available_tpl = ['adminpage', 'metabox', 'metabox-section', 'term-add', 'term-edit', 'user', 'widget'];

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var string
     */
    protected $script;

    /**
     * @var string
     */
    protected $style;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $textdomain = 'zeusfield';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new FieldModel();

        // Set default value
        $defaults = (array) $this->getDefaults();
        $this->defaults = array_merge(['default' => ''], $defaults);

        // Initialize configurations
        $this->getModel()->setAdminscripts((array) $this->adminscripts);
        $this->getModel()->setAdminstyles((array) $this->adminstyles);
        $this->getModel()->setDefaults((array) $this->defaults);
        $this->getModel()->setScript((string) $this->script);
        $this->getModel()->setStyle((string) $this->style);
        $this->getModel()->setTemplate((string) $this->template);
    }

    /**
     * Render assets' component.
     *
     * @return array
     */
    public function assets() : array
    {
        // Retrieve path to Resources and shortname's class
        $path = dirname(dirname($this->getClass()['resources'])).S.'assets'.S;

        // Get assets
        $adminscripts = $this->getModel()->getAdminscripts();
        $adminstyles = $this->getModel()->getAdminstyles();
        $script = $this->getModel()->getScript();
        $style = $this->getModel()->getStyle();

        // Do nothing if all empty
        if (empty($adminscripts) && empty($adminstyles) && empty($script) && empty($style)) {
            return [];
        }

        $assets = [
            'scripts' => [],
            'styles' => []
        ];

        // Admin scripts
        if (!empty($adminscripts)) {
            $assets['scripts'] = array_merge($assets['scripts'], $adminscripts);
        }

        // Admin styles
        if (!empty($adminstyles)) {
            $assets['styles'] = array_merge($assets['styles'], $adminstyles);
        }

        // Scripts
        if (!empty($script)) {
            $assets['scripts'] = array_merge($assets['scripts'], [
                Helpers::urlize($script) => $path.$script
            ]);
        }

        // Styles
        if (!empty($style)) {
            $assets['styles'] = array_merge($assets['styles'], [
                Helpers::urlize($style) => $path.$style
            ]);
        }

        return $assets;
    }

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
    public static function build($identifier, $options = []) : Field
    {
        // Get instance
        try {
            $field = self::getInstance();
        } catch (Exception $e) {
            throw new FieldException(Translate::t('field.errors.class_is_not_defined'));
        }

        // Check ID
        if (is_string($identifier) && empty($identifier)) {
            throw new FieldException(sprintf(
                Translate::t('field.errors.field_id_is_not_defined'),
                $field->getClass()['name']
            ));
        }

        // Set identifier
        $field->getModel()->setIdentifier($identifier);

        // Set options
        $options = array_merge($field->getModel()->getDefaults(), ['name' => $identifier], $options);
        $field->getModel()->setOptions($options);

        // Get field
        return $field;
    }

    /**
     * Prepare HTML component for templating.
     *
     * @param  string  $template
     * @param  object  $object
     * @param  string  $type
     *
     * @return array
     */
    public function prepare($template = 'metabox', $object = null, $type = 'default') : array
    {
        // Class
        $class = $this->getClass();

        // Define available templates to extends
        $twigtpl = in_array($template, $this->available_tpl) ? $template : 'metabox';
        $twigtpl = '@core/fields/'.$twigtpl.'.html.twig';

        // Template definitions
        $context = strtolower($class['name']);
        $views   = $class['resources'].S.'views';

        // Get field details
        $defaults   = $this->getModel()->getDefaults();
        $identifier = $this->getModel()->getIdentifier();
        $options    = $this->getModel()->getOptions();
        $tpl        = $this->getModel()->getTemplate();

        // Define default value
        $defaults['default'] = isset($options['default'])
            ? $options['default']
            : (isset($defaults['default']) ? $defaults['default'] : '');

        // Define object to never store `null` value
        $object = is_null($object) ? 0 : $object;

        // Set field value
        $value = $this->value($identifier, $object, $defaults['default'], $type);
        $value = is_string($value) ? stripslashes($value) : $value;

        // Retrieve vars - used by field core system
        $vars = $this->getVars($value, array_merge($defaults, [
            'identifier' => $identifier,
            'name'       => $identifier,
            'value'      => $value,
        ], $options));

        // Set error
        $error = sprintf(Translate::t('field.errors.no_template_found'), $context, $tpl);
        $vars['error'] = isset($vars['error']) ? $vars['error'] : $error;

        // Set parent template in vars
        $vars['template_path'] = $twigtpl;

        // Return template vars
        return [
            'context'  => $context,
            'path'     => $views,
            'template' => $tpl,
            'vars'     => $vars,
        ];
    }

    /**
     * Retrieve Field translations
     *
     * @throws FieldException
     *
     * @return array
     */
    public static function translate() : array
    {
        // Get instance
        try {
            $field = self::getInstance();
        } catch (Exception $e) {
            throw new FieldException(Translate::t('field.errors.class_is_not_defined'));
        }

        // Set translations
        $class = $field->getClass();

        return [
            $field->textdomain => dirname(dirname($class['resources'])).S.'languages'
        ];
    }

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
    public static function value($identifier, $object, $default, $type = 'default')
    {
        $type = in_array($type, ['post', 'term', 'user', 'widget']) ? $type : 'default';
        $sep = '-';

        // Check id
        if (empty($identifier) || null === $identifier) {
            return null;
        }

        // ~

        // Post metaboxes
        if ('post' === $type && $object) {
            $value = Option::getPostMeta($object->ID, $object->post_type.$sep.$identifier, $default);
            return Option::cleanValue($value);
        }

        // ~

        // Term metaboxes
        if ('term' === $type && $object) {
            $value = Option::getTermMeta($object->term_id, $object->taxonomy.$sep.$identifier, $default);
            return Option::cleanValue($value);
        }

        // ~

        // Term metaboxes
        if ('user' === $type && $object) {
            $value = Option::getAuthorMeta($object->ID, $identifier, $default);
            return Option::cleanValue($value);
        }

        // ~

        // Widget metaboxes
        if ('widget' === $type && $object) {
            $value = empty($object) ? $default : $object;
            return Option::cleanValue($value);
        }

        // ~

        // Default action
        return Option::get($identifier, $default);
    }

    /**
     * Prepare defaults.
     *
     * @return array
     */
    abstract protected function getDefaults() : array;

    /**
     * Prepare variables.
     *
     * @param  object  $value
     * @param  array   $contents
     *
     * @return array
     */
    abstract protected function getVars($value, $contents) : array;

    /**
     * Update post value from request.
     * @todo
     */
    //abstract protected function updatePost();
}
