<?php

namespace GetOlympus\Zeus\Widget;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Utils\Translate;
use GetOlympus\Zeus\Widget\WidgetException;
use GetOlympus\Zeus\Widget\WidgetHook;
use GetOlympus\Zeus\Widget\WidgetInterface;
use GetOlympus\Zeus\Widget\WidgetModel;

/**
 * Gets its own widget.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Widget extends Base implements WidgetInterface
{
    /**
     * @var string
     */
    protected $classname;

    /**
     * @var bool
     */
    protected $display_title = true;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $title;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize WidgetModel
        $this->model = new WidgetModel();

        // Update model
        $this->setClassname($this->classname);
        $this->setDisplayTitle($this->display_title);
        $this->setOptions($this->options);
        $this->setSettings($this->settings);
        $this->setTemplate($this->template);
        $this->setTitle($this->title);

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws WidgetException
     */
    public function addFields($fields) : void
    {
        // Check fields
        if (empty($fields)) {
            throw new WidgetException(Translate::t('widget.errors.no_fields'));
        }

        // Update fields
        $this->getModel()->setFields($fields);
    }

    /**
     * Initialization.
     *
     * @throws WidgetException
     */
    protected function init() : void
    {
        $classname = $this->getModel()->getClassname();
        $template  = $this->getModel()->getTemplate();

        // Check classname
        if (empty($classname)) {
            throw new WidgetException(Translate::t('widget.errors.classname_is_not_defined'));
        }

        // Check template value
        if (empty($template)) {
            throw new WidgetException(Translate::t('widget.errors.template_path_is_not_defined'));
        }

        // Check template file
        if (!file_exists($template)) {
            throw new WidgetException(sprintf(
                Translate::t('widget.errors.template_does_not_exist'),
                $template
            ));
        }

        // Register widget
        $this->register();
    }

    /**
     * Register widget.
     */
    protected function register() : void
    {
        // Works on hook
        $widget = $this;

        add_action('widgets_init', function () use ($widget) {
            $hook = new WidgetHook($widget);
            register_widget($hook);
        });
    }

    /**
     * Set classname.
     *
     * @param  string  $classname
     */
    protected function setClassname($classname) : void
    {
        if (empty($classname)) {
            return;
        }

        $this->getModel()->setClassname($classname);
    }

    /**
     * Set title display.
     *
     * @param  bool    $display_title
     */
    protected function setDisplayTitle($display_title) : void
    {
        if (empty($display_title)) {
            return;
        }

        $this->getModel()->setDisplayTitle($display_title);
    }

    /**
     * Set options.
     *
     * @param  array   $options
     */
    protected function setOptions($options) : void
    {
        if (empty($options)) {
            return;
        }

        $o = $this->getModel()->getOptions();
        $o = array_merge($o, $options);

        $this->getModel()->setOptions($o);
    }

    /**
     * Set settings.
     *
     * @param  array   $settings
     */
    protected function setSettings($settings) : void
    {
        if (empty($settings)) {
            return;
        }

        $s = $this->getModel()->getSettings();
        $s = array_merge($s, $settings);

        $this->getModel()->setSettings($s);
    }

    /**
     * Set template path.
     *
     * @param  string  $template
     */
    protected function setTemplate($template) : void
    {
        if (empty($template)) {
            return;
        }

        $this->getModel()->setTemplate($template);
    }

    /**
     * Set title.
     *
     * @param  string  $title
     */
    protected function setTitle($title) : void
    {
        if (empty($title)) {
            return;
        }

        $this->getModel()->setTitle($title);
    }

    /**
     * Display widget contents
     *
     * @param  array   $instance
     */
    abstract public function display($instance = []) : void;

    /**
     * Prepare variables.
     */
    abstract protected function setVars() : void;
}
