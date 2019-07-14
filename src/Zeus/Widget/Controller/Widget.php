<?php

namespace GetOlympus\Zeus\Widget\Controller;

use GetOlympus\Zeus\Base\Controller\BaseWidget;
use GetOlympus\Zeus\Field\Controller\Field;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;
use GetOlympus\Zeus\Widget\Implementation\WidgetImplementation;
use GetOlympus\Zeus\Widget\Model\WidgetModel;

/**
 * Gets its own widget.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Widget extends BaseWidget implements WidgetImplementation
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize TermModel
        $this->model = new WidgetModel();

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Initialization.
     */
    public function init()
    {
        // Get user defined class name
        $model_classname = $this->getModel()->getClassname();

        // Set identifier
        $identifier = Helpers::urlize($model_classname);
        $this->getModel()->setIdentifier($identifier);

        // Update classnames
        $classnames = explode(' ', $model_classname);
        $classname = '';

        // Iterate on all class names
        foreach ($classnames as $name) {
            $classname .= (empty($classname) ? '' : ' ').strtolower(Helpers::urlize($name));
        }

        $this->getModel()->setClassname($classname);

        // Update default settings
        $settings = $this->getModel()->getSettings();
        $this->getModel()->setSettings(array_merge([
            'classname' => $classname,
            'description' => Translate::t('widget.labels.description'),
        ], $settings));

        // Create the widget
        parent::__construct(
            $this->getModel()->getIdentifier(),
            $this->getModel()->getTitle(),
            $this->getModel()->getSettings(),
            $this->getModel()->getOptions()
        );

        // Update alternative options
        $this->alt_option_name = $classname;

        // Add theme actions
        add_action('save_post', [&$this, 'flush_widget_cache']);
        add_action('deleted_post', [&$this, 'flush_widget_cache']);
        add_action('switch_theme', [&$this, 'flush_widget_cache']);
    }

    /**
     * Cache the widget.
     *
     * @param  array   $args
     * @param  string  $content
     * @return string  $content
     */
    public function cache_widget($args, $content)
    {
        /**
         * Retrieve widget classname as cache key.
         *
         * @param  string  $classname
         * @return string  $classname
         */
        wp_cache_set(
            $this->getModel()->getIdentifier(),
            [$args['widget_id'] => $content],
            'widget'
        );

        return $content;
    }

    /**
     * Flush the widget cache.
     *
     * @return void
     **/
    public function flush_widget_cache()
    {
        wp_cache_delete($this->getModel()->getIdentifier(), 'widget');
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @return void
     */
    public function form($instance)
    {
        $fields = $this->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
        }

        // Add Title field from default mode
        $new_title = [
            'special' => [
                'id'    => 'title',
                'title' => Translate::t('widget.labels.field_title'),
            ],
        ];

        // Add Title field from `olympus-text-field` component
        if (class_exists('\\GetOlympus\\Field\\Text')) {
            $new_title = \GetOlympus\Field\Text::build('title', [
                'title' => Translate::t('widget.labels.field_title'),
            ]);
        }

        // Add title on 1st place
        array_unshift($fields, $new_title);
        unset($new_title);

        $vars = [];

        // Prepare admin scripts and styles
        $assets = [
            'scripts' => [],
            'styles'  => [],
        ];

        // Get fields
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            $id = (string) $field->getModel()->getIdentifier();

            if (empty($id)) {
                continue;
            }

            // Update scripts and styles
            $fieldassets = $field->assets();

            if (!empty($fieldassets)) {
                $assets['scripts'] = array_merge($assets['scripts'], $fieldassets['scripts']);
                $assets['styles']  = array_merge($assets['styles'], $fieldassets['styles']);
            }

            // Prepare fields to be displayed
            $vars['fields'][] = $field->prepare('widget');
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'widget.html.twig', $vars, $assets);
        $render->view();
    }

    /**
     * Get cached widget.
     *
     * @param  array   $args
     * @return boolean true|false
     */
    public function get_cached_widget($args)
    {
        $cache = wp_cache_get($this->getModel()->getIdentifier(), 'widget');

        if (!is_array($cache)) {
            $cache = [];
        }

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return true;
        }

        return false;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param  array   $new_instance
     * @param  array   $old_instance
     * @return array   $instance
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        // Get vars
        foreach ($new_instance as $k => $i) {
            $instance[$k] = isset($new_instance[$k]) ? strip_tags($new_instance[$k]) : false;
        }

        // Flush current widget cache.
        $this->flush_widget_cache();

        // Get options from cache to renew them.
        $alloptions = wp_cache_get('alloptions', 'options');

        // Delete options.
        if (isset($alloptions[$this->getModel()->getIdentifier()])) {
            delete_option($this->getModel()->getIdentifier());
        }

        // Return new data.
        return $instance;
    }

    /**
     * HTML at the start of a widget.
     *
     * @param  array   $args
     * @param  array   $instance
     * @param  string  $title
     */
    public function widget_start($args, $instance, $title)
    {
        echo $args['before_widget'];

        if ($title && $this->getModel()->getDisplayTitle()) {
            echo $args['before_title'].$title.$args['after_title'];
        }
    }

    /**
     * HTML at the end of a widget.
     *
     * @param  array   $args
     * @return string  $after_widget
     */
    public function widget_end($args)
    {
        echo $args['after_widget'];
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param  array   $args
     * @param  array   $instance
     * @return void
     **/
    public function widget($args, $instance)
    {
        global $wpdb;

        // Try to get cached widget.
        $cache = wp_cache_get($this->getModel()->getIdentifier(), 'widget');

        if (!is_array($cache)) {
            $cache = [];
        }

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = null;
        }

        // If all elements are in cache, no need to construct the output again.
        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        // Start output buffering.
        ob_start();

        // Extract data.
        extract($args, EXTR_SKIP);

        // Title
        $title = empty($instance['title']) ? '' : $instance['title'];
        $title = apply_filters('widget_title', $title, $instance, $this->getModel()->getIdentifier());

        // Display content widget
        $this->widget_start($args, $instance, $title);
        $this->display($instance);
        $this->widget_end($args);

        // Renew the cache.
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set($this->getModel()->getIdentifier(), $cache, 'widget');
    }

    /**
     * Display widget contents
     *
     * @param  array   $instance
     */
    abstract public function display($instance = []);

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
