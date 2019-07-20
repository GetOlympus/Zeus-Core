<?php

namespace GetOlympus\Zeus\Widget\Implementation;

/**
 * Widget implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface WidgetImplementation
{
    /**
     * Initialization.
     */
    public function init();

    /**
     * Cache the widget.
     *
     * @param  array   $args
     * @param  string  $content
     *
     * @return string
     */
    public function cache_widget($args, $content);

    /**
     * Flush the widget cache.
     *
     * @return void
     **/
    public function flush_widget_cache();

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @return void
     */
    public function form($instance);

    /**
     * Get cached widget.
     *
     * @param  array   $args
     *
     * @return boolean
     */
    public function get_cached_widget($args);

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param  array   $new_instance
     * @param  array   $old_instance
     *
     * @return array
     */
    public function update($new_instance, $old_instance);

    /**
     * HTML at the start of a widget.
     *
     * @param  array   $args
     * @param  array   $instance
     * @param  string  $title
     */
    public function widget_start($args, $instance, $title);

    /**
     * HTML at the end of a widget.
     *
     * @param  array   $args
     *
     * @return string
     */
    public function widget_end($args);

    /**
     * Outputs the HTML for this widget.
     *
     * @param  array   $args
     * @param  array   $instance
     *
     * @return void
     **/
    public function widget($args, $instance);
}
