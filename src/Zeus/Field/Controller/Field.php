<?php

namespace GetOlympus\Zeus\Field\Controller;

use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Field\Controller\FieldInterface;
use GetOlympus\Zeus\Field\Exception\FieldException;
use GetOlympus\Zeus\Field\Model\FieldModel;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Abstract class to define all field context with authorized fields, how to
 * write some functions and every usefull checks.
 *
 * @package    OlympusZeusCore
 * @subpackage Field\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Field extends Base implements FieldInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new FieldModel();

        // Initialize
        $this->setVars();
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
        $hasid = $field->getModel()->getHasId();

        // Check ID
        if ($hasid && empty($id)) {
            throw new FieldException(sprintf(Translate::t('field.errors.field_id_is_not_defined'), $class));
        }

        // Set ID
        $contents['id'] = $id;

        // Set contents and details
        $field->getModel()->setContents($contents);
        $field->getModel()->setDetails($details);

        // Get field
        return $field;
    }

    /**
     * Retrieve field value
     *
     * @param string $id
     * @param array $details
     * @param object $default
     *
     * @return string|integer|array|object|boolean|null
     */
    public static function getValue($id, $details, $default)
    {
        return Option::getValue($id, $details, $default);
    }

    /**
     * Render HTML component.
     *
     * @param array     $contents
     * @param array     $details
     * @param boolean   $renderView
     * @param string    $context
     */
    public function render($contents = [], $details = [], $renderView = true, $context = 'field')
    {
        // Merge datum
        $contents = array_merge($this->getModel()->getContents(), $contents);
        $details = array_merge($this->getModel()->getDetails(), $details);

        // Get context
        $class = $this->getClass();
        $context = strtolower($class['name']);

        // Get template to extends
        $template = isset($details['template']) ? $details['template'] : '';
        $contents['template_path'] = $this->setExtendedTemplate($template);

        // Get vars and tpl data
        $this->getVars($contents, $details);
        $tpl = [
            'hasId' => $this->getModel()->getHasId(),
            'template' => $this->getModel()->getTemplate(),
            'vars' => $this->getModel()->getVars(),
            'context' => $context
        ];

        // Set error
        $error = sprintf(Translate::t('field.errors.no_template_found'), $tpl['context'], $tpl['template']);
        $tpl['vars']['error'] = isset($tpl['vars']['error']) ? $tpl['vars']['error'] : $error;

        // Render view or return values
        if ($renderView) {
            Render::view($tpl['template'], $tpl['vars'], $tpl['context']);
        } else {
            return $tpl;
        }
    }

    /**
     * Render assets' component.
     *
     * @return array $assets
     */
    public function renderAssets()
    {
        // Retrieve path to Resources and shortname's class
        $class = $this->getClass();
        $path = $class['resources'].S.'assets'.S;

        // Get assets
        $script = $this->getModel()->getScript();
        $style = $this->getModel()->getStyle();

        $assets = [];

        // Do nothing if all empty
        if (empty($script) && empty($style)) {
            return $assets;
        }

        // Scripts
        if (!empty($script)) {
            $assets[$script] = $path.$script;
        }

        // Styles
        if (!empty($style)) {
            $assets[$style] = $path.$style;
        }

        return $assets;
    }

    /**
     * Define the right template to extend.
     *
     * @param   string  $template
     * @return  string  $extend_template
     */
    public function setExtendedTemplate($template = 'metabox')
    {
        // Define available templates to extends
        $available = ['adminpage','metabox','term-add','term-edit','user','widget'];

        // Work on template
        $twigtpl = in_array($template, $available) ? $template : 'metabox';

        // Return template to extend
        return '@core/fields/'.$twigtpl.'.html.twig';
    }

    /**
     * Prepare HTML component.
     *
     * @param array $content
     * @param array $details
     */
    abstract protected function getVars($content, $details = []);

    /**
     * Prepare variables.
     */
    abstract protected function setVars();
}
