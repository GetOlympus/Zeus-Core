<?php

namespace GetOlympus\Hera\Widget\Controller;

use GetOlympus\Hera\Base\Controller\BaseWidget;
use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;
use GetOlympus\Hera\Widget\Controller\WidgetInterface;
use GetOlympus\Hera\Widget\Model\WidgetModel;

/**
 * Gets its own widget.
 *
 * @package Olympus Hera
 * @subpackage Widget\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Widget extends BaseWidget implements WidgetInterface
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
        // Update classname
        $classnames = explode(' ', $this->getModel()->getClassname());
        $classname = '';

        // Iterate on all class names
        foreach ($classnames as $name) {
            $classname .= strtolower(Render::urlize($name, '-'));
        }

        $this->getModel()->setClassname($classname);

        // Update default settings
        $settings = $this->getModel()->getSettings();
        $this->getModel()->setSettings(array_merge([
            'classname' => $classname,
            'description' => Translate::t('widget.settings.description'),
        ], $settings));

        // Create the widget
        parent::__construct(
            $this->getModel()->getClassname(),
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
     * @param array $args
     * @param string $content
     * @return string $content
     */
    public function cache_widget($args, $content)
    {
        /**
         * Retrieve widget classname as cache key.
         *
         * @param string $classname
         * @return string $classname
         */
        wp_cache_set(
            $this->getModel()->getClassname(),
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
        wp_cache_delete($this->getModel()->getClassname(), 'widget');
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

        // Add Title field from `olympus-text-field` component
        if (class_exists('\\GetOlympus\\Field\\Text')) {
            $new_title = \GetOlympus\Field\Text::build('title', [
                'title' => Translate::t('widget.fields.title'),
            ]);
        }
        // Add Title field from default mode
        else {
            $new_title = [
                'special' => [
                    'id' => 'title',
                    'title' => Translate::t('widget.fields.title'),
                ],
            ];
        }

        // Add title on 1st place
        array_unshift($fields, $new_title);
        unset($new_title);

        $vars = [];

        // Get fields
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            if (is_array($field) && isset($field['special'])) {
                $ctn = (array) $field['special'];
                $hasId = true;
            } else {
                $ctn = (array) $field->getModel()->getContents();
                $hasId = (boolean) $field->getModel()->getHasId();
            }

            // Check fields
            if (empty($ctn) || !$hasId) {
                continue;
            }

            // Does the field have an ID
            if (!isset($ctn['id']) || empty($ctn['id'])) {
                continue;
            }

            // Value
            $id = isset($ctn['id']) ? $ctn['id'] : '';
            $value = !empty($id) && isset($instance[$id]) ? $instance[$id] : '';

            // Id and name
            $ctn['id'] = $this->get_field_id($id);
            $ctn['name'] = $this->get_field_name($id);

            // Get render field
            $vars['fields'][] = $field->render($ctn, [
                'template' => 'widget',
                'widget_value' => $value
            ], false);
        }

        // Render view
        Render::view('widget.html.twig', $vars, 'widget');
    }

    /**
     * Get cached widget.
     *
     * @param array $args
     * @return boolean true|false
     */
    public function get_cached_widget($args)
    {
        $cache = wp_cache_get($this->getModel()->getClassname(), 'widget');

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
     * @param array $new_instance
     * @param array $old_instance
     * @return array $instance
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
        if (isset($alloptions[$this->getModel()->getClassname()])) {
            delete_option($this->getModel()->getClassname());
        }

        // Return new data.
        return $instance;
    }

    /**
     * HTML at the start of a widget.
     *
     * @param array     $args
     * @param array     $instance
     * @param string    $title
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
     * @param  array $args
     * @return string $after_widget
     */
    public function widget_end($args)
    {
        echo $args['after_widget'];
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array $args
     * @param array $instance
     * @return void
     **/
    public function widget($args, $instance)
    {
        global $wpdb;

        // Try to get cached widget.
        $cache = wp_cache_get($this->getModel()->getClassname(), 'widget');

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
        $title = apply_filters('widget_title', $title, $instance, $this->getModel()->getClassname());

        // Display content widget
        $this->widget_start($args, $instance, $title);
        $this->display($instance);
        $this->widget_end($args);

        // Renew the cache.
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set($this->getModel()->getClassname(), $cache, 'widget');
    }

    /**
     * Display widget contents
     *
     * @param array $instance Contains all field data.
     */
    abstract public function display($instance = []);

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
