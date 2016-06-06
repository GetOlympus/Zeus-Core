<?php

namespace GetOlympus\Hera\Field\Controller;

use GetOlympus\Hera\Field\Controller\FieldInterface;
use GetOlympus\Hera\Field\Exception\FieldException;
use GetOlympus\Hera\Field\Model\FieldModel;
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

abstract class Field implements FieldInterface
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
     * @param string    $id
     * @param array     $contents
     * @param array     $details
     */
    public static function build($id, $contents = [], $details = [])
    {
        // Get instance
        try {
            $field = self::getInstance();
        } catch (Exception $e) {
            throw new FieldException(Translate::t('field.errors.class_is_not_defined'));
        }

        // Set class
        $class = get_class($field);

        // Check ID
        if ($field->getHasId() && empty($id)) {
            // Error
            throw new FieldException(sprintf(Translate::t('field.errors.field_id_is_not_defined'), $class));
        } else if ($field->getHasId()) {
            // Set ID
            $contents['id'] = $id;
        }

        // Set contents and details
        $field->field->setContents($contents);
        $field->field->setDetails($details);

        // Get field
        return $field;
    }

    /**
     * Gets the value of field.
     *
     * @return FieldModel
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Define if field has an ID or not.
     *
     * @return boolean $hasId
     */
    public static function getHasId()
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
    public static function getIsAuthorized()
    {
        return self::getInstance()->isAuthorized;
    }

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
     * @param array $details
     * @param boolean $renderView
     * @param string $context
     */
    public function render($details = [], $renderView = true, $context = 'field')
    {
        $contents = $this->field->getContents();
        $details = array_merge($this->field->getDetails(), $details);

        // Get vars
        $this->getVars($contents, $details);
        $tpl = [
            'hasId' => $this->field->getHasId(),
            'template' => $this->field->getTemplate(),
            'vars' => $this->field->getVars()
        ];

        // Get context
        $class = new \ReflectionClass(get_class($this));
        $context = strtolower($class->getShortName());

        // Render view or return values
        if ($renderView) {
            Render::view($tpl['template'], $tpl['vars'], $context);
        }
        else {
            return $tpl;
        }
    }
}
