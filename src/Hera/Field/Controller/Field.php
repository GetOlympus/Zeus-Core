<?php

namespace GetOlympus\Hera\Field\Controller;

use GetOlympus\Hera\Field\Model\Field as FieldModel;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Abstract class to define all field context with authorized fields, how to
 * write some functions and every usefull checks.
 *
 * @package Olympus Hera
 * @subpackage Field\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Field
{
    /**
     * @var FieldModel
     */
    protected $field;

    /**
     * @var string
     */
    protected $faIcon = 'fa-circle-o';

    /**
     * @var boolean
     */
    protected $hasId = true;

    /**
     * @var Field
     */
    protected static $instance = null;

    /**
     * @var boolean
     */
    protected $isAuthorized = true;

    /**
     * @var string
     */
    protected $template = 'field.html.twig';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->field = new FieldModel();

        $this->field->setFaIcon($this->faIcon);
        $this->field->setHasId($this->hasId);
        $this->field->setIsAuthorized($this->isAuthorized);
        $this->field->setTemplate($this->template);
    }

    /**
     * Build Field component.
     *
     * @param string $type
     * @param string $id
     * @param array $alreadyused
     * @param boolean $special
     *
     * @return $class|false
     */
    public static function build($type, $id, $alreadyused = [], $special = false)
    {
        // Prepare error
        $error = [
            'error' => true,
            'template' => '@notification/notification.html.twig',
            'vars' => ['content' => ''],
        ];

        // Check type integrity
        if (empty($type)) {
            $error['vars']['content'] = Translate::t('field.errors.type_is_not_defined');

            return $error;
        }

        // Set class
        $class = 'GetOlympus\\Field\\'.ucfirst($type);

        // Check if the class file exists
        if (!class_exists($class)) {
            $error['vars']['content'] = sprintf(Translate::t('field.errors.class_is_not_defined'), $class);

            return $error;
        }

        // Check if the asked field is unknown
        if (!$class::getIsauthorized() && !$special) {
            $error['vars']['content'] = sprintf(Translate::t('field.errors.field_is_unknown'), $id);

            return $error;
        }

        // Check if field needs an id
        if ($class::getHasid() && !$id) {
            $error['vars']['content'] = sprintf(Translate::t('field.errors.field_id_is_not_defined'), $type);

            return $error;
        }

        // Check if field needs an id
        if ($class::getHasid() && in_array($id, $alreadyused)) {
            $error['vars']['content'] = sprintf(Translate::t('field.errors.field_id_already_used'), $id);

            return $error;
        }

        // Instanciate class
        $field = new $class();

        // Return $field
        return $field;
    }

    /**
     * Get class name.
     *
     * @return string $classname
     */
    public function getClassName()
    {
        return static::class;
    }

    /**
     * Gets the value of field.
     *
     * @return FieldModel
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * Define if field has an ID or not.
     *
     * @return boolean $hasId
     */
    public static function getHasid()
    {
        return self::getInstance()->hasId;
    }

    /**
     * Gets the value of instance.
     *
     * @return Field
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Define if field is authorized or not.
     *
     * @return boolean $isAuthorized
     */
    public static function getIsauthorized()
    {
        return self::getInstance()->isAuthorized;
    }

    /**
     * Gets the value of field.
     *
     * @return FieldModel
     */
    /*protected function getField()
    {
        return $this->field;
    }*/

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
    public static function getValue($details, $default, $id = '', $multiple = false)
    {
        return Option::getFieldValue($details, $default, $id, $multiple);
    }

    /**
     * Prepare HTML component.
     *
     * @param array $content
     * @param array $details
     */
    abstract protected function getVars($content, $details = []);

    /**
     * Render HTML component.
     *
     * @param array $content
     * @param array $details
     * @param boolean $renderView
     * @param string $context
     */
    public function render($content, $details = [], $renderView = true, $context = 'field')
    {
        $this->getVars($content, $details);
        $tpl = [
            'template' => '@'.$context.'/'.$this->field->getTemplate(),
            'vars' => $this->field->getVars()
        ];

        // Render view or return values
        if ($renderView) {
            Render::view($tpl['template'], $tpl['vars'], $context);
        }
        else {
            return $tpl;
        }
    }
}
