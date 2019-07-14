<?php

namespace GetOlympus\Zeus\Option\Controller;

use GetOlympus\Zeus\Option\Interface\OptionInterface;

/**
 * Works with WP options.
 *
 * @package    OlympusZeusCore
 * @subpackage Option\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Option implements OptionInterface
{
    /**
     * Force add a value into options
     *
     * @param  string  $option
     * @param  string  $value
     * @param  string  $deprecated
     * @param  string  $autoload
     */
    public static function add($option, $value, $deprecated = '', $autoload = 'yes')
    {
        add_option($option, $value, $deprecated, $autoload);
    }

    /**
     * Set a value into options
     *
     * @param  string  $option
     */
    public static function delete($option)
    {
        delete_option($option);
    }

    /**
     * Return a value from options
     *
     * @param  string  $option
     * @param  string  $default
     * @param  string  $item
     * @return mixed   $value
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
         * @var    string  $option
         * @param  array   $value
         * @return array   $value
         */
        $value = apply_filters('ol_zeus_option_get_'.$option, $value);

        // Return value
        return is_array($value) && isset($value[$item]) ? $value[$item] : $value;
    }

    /**
     * Set a value into options
     *
     * @param  string  $option
     * @param  string  $value
     * @param  string  $type
     * @param  integer $id
     */
    public static function set($option, $value, $type = '', $id = 0)
    {
        /**
         * Works on option's value.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @return array   $value
         */
        $value = apply_filters('ol_zeus_option_set_'.$option, $value);

        // Set value into DB with autoload
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
     * @param  string  $option
     * @param  string  $value
     */
    public static function update($option, $value)
    {
        update_option($option, $value);
    }

    /**
     * Clean details on value
     *
     * @param  mixed   $value
     * @return mixed   $value
     */
    public static function cleanValue($value)
    {
        if (is_array($value)) {
            $new_value = [];

            foreach ($value as $k => $v) {
                $new_value[$k] = self::cleanValue($v);
            }

            return $new_value;
        }

        return stripslashes($value);
    }

    /**
     * Get a value from user options
     *
     * @param  string  $user_id
     * @param  string  $option
     * @return mixed   $value
     */
    public static function getAuthorMeta($user_id, $option)
    {
        return get_the_author_meta($option, $user_id);
    }

    /**
     * Force update a value into user options
     *
     * @param  string  $user_id
     * @param  string  $option
     * @param  string  $value
     */
    public static function updateAuthorMeta($user_id, $option, $value)
    {
        update_user_meta($user_id, $option, $value);
    }

    /**
     * Get a value from post options
     *
     * @param  string  $post_id
     * @param  string  $option
     * @return mixed   $value
     */
    public static function getPostMeta($post_id, $option)
    {
        return get_post_meta($post_id, $option, true);
    }

    /**
     * Force update a value into post options
     *
     * @param  string  $post_id
     * @param  string  $option
     * @param  string  $value
     */
    public static function updatePostMeta($post_id, $option, $value)
    {
        update_post_meta($post_id, $option, $value);
    }

    /**
     * Get a value from term options
     *
     * @param  string  $term_id
     * @param  string  $option
     * @param  mixed   $default
     * @return mixed   $value
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
     * @param  string  $term_id
     * @param  string  $option
     * @param  string  $value
     */
    public static function updateTermMeta($term_id, $option, $value)
    {
        update_term_meta($term_id, $option, $value);
    }
}
