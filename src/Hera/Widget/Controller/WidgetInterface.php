<?php

namespace GetOlympus\Hera\Widget\Controller;

/**
 * Widget interface.
 *
 * @package Olympus Hera
 * @subpackage Widget\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface WidgetInterface
{
    /**
     * Initialization.
     */
    public function init();

    /**
     * Cache the widget.
     *
     * @param array $args
     * @param string $content
     * @return string $content
     */
    public function cache_widget($args, $content);

    /**
     * Flush the widget cache.
     *
     * @return void
     **/
    public function flush_widget_cache();

    /**
     * Get cached widget.
     *
     * @param array $args
     * @return boolean true|false
     */
    public function get_cached_widget($args);

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @return void
     */
    public function form($instance);

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array $instance
     */
    public function update($new_instance, $old_instance);

    /**
     * HTML at the start of a widget.
     *
     * @param array     $args
     * @param array     $instance
     * @param string    $title
     */
    public function widget_start($args, $instance, $title);

    /**
     * HTML at the end of a widget.
     *
     * @param  array $args
     * @return string $after_widget
     */
    public function widget_end($args);

    /**
     * Outputs the HTML for this widget.
     *
     * @param array $args
     * @param array $instance
     * @return void
     **/
    public function widget($args, $instance);
}
