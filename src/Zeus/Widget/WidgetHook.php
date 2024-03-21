<?php

namespace GetOlympus\Zeus\Widget;

use GetOlympus\Zeus\Base\BaseWidget;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Works with Widget Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.0
 *
 */

class WidgetHook extends BaseWidget
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * Constructor.
     *
     * @param  Widget  $widget
     */
    public function __construct($widget)
    {
        $this->widget = $widget;

        // Get user defined class name
        $model_classname = $this->widget->getModel()->getClassname();

        // Set identifier
        $identifier = Helpers::urlize($model_classname);
        $this->widget->getModel()->setIdentifier($identifier);

        // Update classnames
        $classnames = explode(' ', $model_classname);
        $classname = '';

        // Iterate on all class names
        foreach ($classnames as $name) {
            $classname .= (empty($classname) ? '' : ' ').strtolower(Helpers::urlize($name));
        }

        $this->widget->getModel()->setClassname($classname);

        // Update default settings
        $settings = $this->widget->getModel()->getSettings();
        $this->widget->getModel()->setSettings(array_merge([
            'classname'   => $classname,
            'description' => Translate::t('widget.labels.description'),
        ], $settings));

        // Create the widget
        parent::__construct(
            $this->widget->getModel()->getIdentifier(),
            $this->widget->getModel()->getTitle(),
            $this->widget->getModel()->getSettings(),
            $this->widget->getModel()->getOptions()
        );

        // Update alternative options
        $this->alt_option_name = $classname;

        // Add theme actions
        add_action('save_post', [$this, 'flush_widget_cache']);
        add_action('deleted_post', [$this, 'flush_widget_cache']);
        add_action('switch_theme', [$this, 'flush_widget_cache']);
    }

    /**
     * Cache the widget.
     *
     * @param  array   $args
     * @param  string  $content
     *
     * @return string
     */
    public function cache_widget($args, $content) // phpcs:ignore
    {
        /**
         * Retrieve widget classname as cache key.
         *
         * @param  string  $classname
         *
         * @return string
         */
        wp_cache_set(
            $this->id_base,
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
    public function flush_widget_cache() // phpcs:ignore
    {
        wp_cache_delete($this->id_base, 'widget');
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @return void
     */
    public function form($instance)
    {
        $fields = $this->widget->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
        }

        // Add Title field from `olympus-text-field` component
        if (class_exists('\\GetOlympus\\Dionysos\\Field\\Text')) {
            $new_title = \GetOlympus\Dionysos\Field\Text::build('title', [
                'title' => Translate::t('widget.labels.field_title'),
            ]);

            // Add title on 1st place
            array_unshift($fields, $new_title);
            unset($new_title);
        }

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
            $fieldvars = $field->prepare('widget', (isset($instance[$id]) ? $instance[$id] : ''), 'widget');
            $fieldvars['vars']['name'] = $this->get_field_name($fieldvars['vars']['name']);

            // Store field vars
            $vars['fields'][] = $fieldvars;
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'widget.html.twig', $vars, $assets);
        $render->view();
    }

    /**
     * Get cached widget.
     *
     * @param  array   $args
     *
     * @return bool
     */
    public function get_cached_widget($args) // phpcs:ignore
    {
        $cache = wp_cache_get($this->id_base, 'widget');

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
     *
     * @return array
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
        if (isset($alloptions[$this->id_base])) {
            delete_option($this->id_base);
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
    public function widget_start($args, $instance, $title) // phpcs:ignore
    {
        echo apply_filters('ol.zeus.widget_start', $args['before_widget']);

        if ($title && $this->widget->getModel()->getDisplayTitle()) {
            echo $args['before_title'].$title.$args['after_title'];
        }
    }

    /**
     * HTML at the end of a widget.
     *
     * @param  array   $args
     *
     * @return string
     */
    public function widget_end($args) // phpcs:ignore
    {
        echo apply_filters('ol.zeus.widget_end', $args['after_widget']);
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param  array   $args
     * @param  array   $instance
     *
     * @return void
     **/
    public function widget($args, $instance)
    {
        global $wpdb;

        // Try to get cached widget.
        $cache = wp_cache_get($this->id_base, 'widget');

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
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        // Display content widget
        $this->widget_start($args, $instance, $title);
        $this->widget->display($instance);
        $this->widget_end($args);

        // Renew the cache.
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set($this->id_base, $cache, 'widget');
    }
}
