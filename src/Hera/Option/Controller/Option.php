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
     * Set a value into options
     *
     * @param string    $option
     * @param string    $value
     * @param string    $type
     * @param integer   $id
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
     * Force update a value into options
     *
     * @param string $option
     * @param string $value
     */
    public static function update($option, $value)
    {
        update_option($option, $value);
    }

    /**
     * Clean details on value
     *
     * @param array     $value
     * @return mixed    $value
     */
    public static function cleanValue($value)
    {
        if (is_array($value)) {
            $new_value = [];

            foreach ($value as $k => $v) {
                $new_value[$k] = stripslashes($v);
            }

            return $new_value;
        }

        return stripslashes($value);
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

        // ~

        // Post field?
        $post = isset($details['post']) ? $details['post'] : 0;

        // Post metaboxes
        if (!empty($post)) {
            $value = self::getPostMeta($post->ID, $post->post_type.'-'.$id);
            $value = empty($value) ? $default : $value;

            return self::cleanValue($value);
        }

        // ~

        // Term field?
        $term = isset($details['term']) ? $details['term'] : 0;

        // Term metaboxes
        if (!empty($term)) {
            $value = self::getTermMeta($term->term_id, $term->taxonomy.'-'.$id, $default);
            $value = empty($value) ? $default : $value;

            return self::cleanValue($value);
        }

        // ~

        // User field?
        $user = isset($details['user']) ? $details['user'] : 0;

        // Term metaboxes
        if (!empty($user)) {
            $value = self::getAuthorMeta($user->ID, $id);
            $value = empty($value) ? $default : $value;

            return self::cleanValue($value);
        }

        // ~

        // Widget field?
        $widget_value = isset($details['widget_value']) ? $details['widget_value'] : '';

        // Widget metaboxes
        if (!empty($widget_value)) {
            return self::cleanValue($widget_value);
        }

        // ~

        // Default action
        $option = isset($details['option']) ? $details['option'] : '';
        $value = !empty($option) ? self::get($option, $default) : self::get($id, $default);

        return self::cleanValue($value);
    }

    /**
     * Get a value from user options
     *
     * @param string    $user_id
     * @param string    $option
     * @return mixed    $value
     */
    public static function getAuthorMeta($user_id, $option)
    {
        return get_the_author_meta($option, $user_id);
    }

    /**
     * Force update a value into user options
     *
     * @param string $user_id
     * @param string $option
     * @param string $value
     */
    public static function updateAuthorMeta($user_id, $option, $value)
    {
        update_usermeta($user_id, $option, $value);
    }

    /**
     * Get a value from post options
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
     * Force update a value into post options
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
     * Get a value from term options
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
     * Force update a value into term options
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
