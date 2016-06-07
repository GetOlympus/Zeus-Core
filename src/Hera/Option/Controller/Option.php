<?php

namespace GetOlympus\Hera\Option\Controller;

use GetOlympus\Hera\Option\Controller\OptionInterface;

/**
 * Works with WP options.
 *
 * @package Olympus Hera
 * @subpackage Option\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Option implements OptionInterface
{
    /**
     * Force add a value into options
     *
     * @param string $option
     * @param string $value
     * @param string $deprecated
     * @param string $autoload
     */
    public static function add($option, $value, $deprecated = '', $autoload = 'no')
    {
        add_option($option, $value, $deprecated, $autoload);
    }

    /**
     * Set a value into options
     *
     * @param string $option
     */
    public static function delete($option)
    {
        delete_option($option);
    }

    /**
     * Return a value from options
     *
     * @param string $option
     * @param string $default
     * @param string $item
     * @return mixed $value
     */
    public static function get($option, $default = '', $item = '')
    {
        // Get value from DB
        $value = get_option($option);

        // Put the default value if not
        $value = false === $value ? $default : $value;

        /**
         * Works on option's value.
         *
         * @var string      $option
         * @param array     $value
         * @return array    $value
         */
        $value = apply_filters('olh_option_get_'.$option, $value);

        // Return value
        return is_array($value) && isset($value[$item]) ? $value[$item] : $value;
    }

    /**
     * Retrieve field value
     *
     * @param array     $details
     * @param object    $default
     * @param string    $id
     * @param boolean   $multiple
     * @return mixed    $value
     */
    public static function getFieldValue($details, $default, $id = '', $multiple = false)
    {
        // Check id
        if (empty($id)) {
            return null;
        }

        // Post field?
        $post = isset($details['post']) ? $details['post'] : 0;

        // Post metaboxes
        if (!empty($post)) {
            $value = self::getPostMeta($post->ID, $post->post_type.'-'.$id);
            $value = empty($value) ? $default : $value;

            return !is_array($value) ? stripslashes($value) : $value;
        }

        // Term field?
        $term_id = isset($details['term_id']) ? $details['term_id'] : 0;

        // Term metaboxes
        if (!empty($term_id)) {
            $term = get_term($term_id);
            $slug = $term->slug;

            $value = self::getTermMeta($term_id, $slug.'-'.$id, $default);
            $value = empty($value) ? $default : $value;

            return !is_array($value) ? stripslashes($value) : $value;
        }

        // Widget field?
        $widget_value = isset($details['widget_value']) ? $details['widget_value'] : '';

        // Widget metaboxes
        if (!empty($widget_value)) {
            $value = $widget_value;
            return !is_array($value) ? stripslashes($value) : $value;
        }

        // Default action
        $option = isset($details['option']) ? $details['option'] : '';
        $value = !empty($option) ? self::get($option, $default) : $default;

        return !is_array($value) ? stripslashes($value) : $value;
    }

    /**
     * Force update a value into post options without transient
     *
     * @param string    $post_id
     * @param string    $option
     * @return mixed    $value
     */
    public static function getPostMeta($post_id, $option)
    {
        return get_post_meta($post_id, $option, true);
    }

    /**
     * Force update a value into term options without transient
     *
     * @param string    $term_id
     * @param string    $option
     * @param mixed     $default
     * @return mixed    $value
     */
    public static function getTermMeta($term_id, $option, $default = '')
    {
        if (function_exists('get_term_meta')) {
            // WP 4.4
            $value = get_term_meta($term_id, $option, true);
        } else {
            // Default
            $value = self::get($option, $default);
        }

        return $value;
    }

    /**
     * Set a value into options
     *
     * @param string    $option
     * @param string    $value
     * @param string    $type
     * @param integer   $type
     */
    public static function set($option, $value, $type = '', $id = 0)
    {
        /**
         * Works on option's value.
         *
         * @var string $option
         * @param mixed $value
         * @return array $value
         */
        $value = apply_filters('olh_option_set_'.$option, $value);

        // Set value into DB without autoload
        if (!empty($id)) {
            $func = 'term' === $type ? 'updateTermMeta' : 'updatePostMeta';
            self::$func($id, $option, $value);
        } else if (false === get_option($option)) {
            self::add($option, $value);
        } else {
            self::update($option, $value);
        }
    }

    /**
     * Force update a value into options without transient
     *
     * @param string $option
     * @param string $value
     */
    public static function update($option, $value)
    {
        update_option($option, $value);
    }

    /**
     * Force update a value into post options without transient
     *
     * @param string $post_id
     * @param string $option
     * @param string $value
     */
    public static function updatePostMeta($post_id, $option, $value)
    {
        update_post_meta($post_id, $option, $value);
    }

    /**
     * Force update a value into term options without transient
     *
     * @param string $term_id
     * @param string $option
     * @param string $value
     */
    public static function updateTermMeta($term_id, $option, $value)
    {
        update_term_meta($term_id, $option, $value);
    }
}
