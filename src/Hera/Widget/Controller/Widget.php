<?php

namespace GetOlympus\Hera\Widget\Controller;

use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;
use GetOlympus\Hera\Widget\Controller\WidgetInterface;

/**
 * Gets its own widget.
 *
 * @package Olympus Hera
 * @subpackage Widget\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

if (!class_exists('WP_Widget') && defined('ABSPATH')) {
    require_once ABSPATH.'wp-includes/widgets.php';
}

abstract class Widget extends \WP_Widget implements WidgetInterface
{
    /**
     * @var string
     */
    protected $classname;

    /**
     * @var array
     */
    protected $fields;

    /**
     * The "height" key is never used. For more informations:
     * @see https://core.trac.wordpress.org/browser/tags/4.5.2/src/wp-includes/widgets.php#L490
     *
     * @var array
     */
    protected $options = [
        'height'    => 200,
        'width'     => 250,
    ];

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
     * @var WidgetModel
     */
    protected $widget;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Update vars
        $this->setVars();

        // Set classname
        $this->classname = strtolower(Render::urlize($this->classname));

        // Update default settings
        $this->settings = array_merge([
            'classname' => $this->classname,
            'description' => Translate::t('widget.settings.description'),
        ], $this->settings);

        // Create the widget
        parent::__construct(
            $this->classname,
            $this->title,
            $this->settings,
            $this->options
        );

        // Add alternative options
        $this->alt_option_name = $this->classname;

        // Initialize
        $this->init();
    }

    /**
     * Initialization.
     */
    public function init()
    {
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
            $this->classname,
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
        wp_cache_delete($this->classname, 'widget');
    }

    /**
     * Get cached widget.
     *
     * @param array $args
     * @return boolean true|false
     */
    public function get_cached_widget($args)
    {
        $cache = wp_cache_get($this->classname, 'widget');

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
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @return void
     */
    public function form($instance)
    {
        // Check fields
        if (empty($this->fields)) {
            return;
        }

        // Get fields
        foreach ($this->fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            $ctn = (array) $field->getField()->getContents();
            $hasId = (boolean) $field->getField()->getHasId();

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

            // Display field
            $field->render($ctn, [
                'widget_value' => $value
            ]);
        }
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
        if (isset($alloptions[$this->classname])) {
            delete_option($this->classname);
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

        if ($title) {
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
        $cache = wp_cache_get($this->classname, 'widget');

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
        $title = apply_filters('widget_title', $title, $instance, $this->classname);

        // Display content widget
        $this->widget_start($args, $instance, $title);
        $this->display($instance);
        $this->widget_end($args);

        // Renew the cache.
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set($this->classname, $cache, 'widget');
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
