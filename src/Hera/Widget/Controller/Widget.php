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
     * @var string
     */
    protected $template;

    /**
     * @var WidgetModel
     */
    protected $widget;

    /**
     * Constructor.
     */
    public function __construct(){}

    /**
     * Initialization.
     *
     * @param string $title
     * @param string $classname
     */
    public function init($title, $classname)
    {
        // Set classname
        $this->classname = Render::urlize($classname);

        // Update default settings
        $settings = [
            'classname' => $this->classname,
            'description' => Translate::t('widget.settings.description'),
        ];

        /**
         * Filter the widget settings.
         *
         * @var string $classname
         * @param array $settings
         * @return array $settings
         */
        $settings = apply_filters('olh_widget_'.$this->classname.'_settings', $settings);

        // Create the widget
        parent::__construct(
            $this->classname,
            $title,
            $settings
        );

        // Add alternative options
        $this->alt_option_name = $this->classname;

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
            apply_filters('olh_widget_cached_classname', $this->classname),
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
        wp_cache_delete(apply_filters('olh_widget_cached_classname', $this->classname), 'widget');
    }

    /**
     * Get cached widget.
     *
     * @param array $args
     * @return boolean true|false
     */
    public function get_cached_widget($args)
    {
        $cache = wp_cache_get(apply_filters('olh_widget_cached_classname', $this->classname), 'widget');

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
        foreach ($this->fields as $ctn) {
            // Check fields
            if (empty($ctn)) {
                continue;
            }

            // Get type and id
            $type = isset($ctn['type']) ? $ctn['type'] : '';
            $id = isset($ctn['id']) ? $ctn['id'] : '';

            // Check if we are authorized to use this field in CPTs
            if (empty($type)) {
                continue;
            }

            // Get field instance
            $field = Field::build($type, $id, $usedIds);

            // Update ids
            if (!empty($id)) {
                $usedIds[] = $id;
            }

            // Get details
            $details = wp_parse_args($instance[$id], [
                'prefix' => 'widget',
                'template' => 'widget',
                'widget_value' => $instance[$id]
            ]);

            // Get template
            $tpl = $field->render($ctn, $details);
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
        foreach ($instance as $k => $i) {
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
     * @param  array $args
     * @return string $title
     */
    public function widget_start($args, $instance)
    {
        echo $args['before_widget'];

        $title = empty($instance['title']) ? '' : $instance['title'];
        $title = apply_filters('widget_title', $title, $instance, $this->classname);

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
        $title = apply_filters('widget_title', $instance['title']);

        /**
         * Display content widget.
         *
         * @var string $slug
         * @param string $title
         * @param array $instance
         */
        //do_content('olh_widget_'.$this->classname.'_show', $title, $instance);
        $this->display($instance);

        // Renew the cache.
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set($this->classname, $cache, 'widget');
    }

    /**
     * Display widget contents
     *
     * @param array $instance Contains all field data.
     */
    public function display($instance = [])
    {
        //
    }
}
