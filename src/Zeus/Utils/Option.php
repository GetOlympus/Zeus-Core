<?php

namespace GetOlympus\Zeus\Utils;

/**
 * Works with WP options.
 *
 * @package    OlympusZeusCore
 * @subpackage Utils
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Option
{
    /**
     * Force add a value into options
     *
     * @param  string  $option
     * @param  string  $value
     * @param  string  $deprecated
     * @param  string  $autoload
     */
    public static function add($option, $value, $deprecated = '', $autoload = 'yes') : void
    {
        /**
         * Fires before adding option into database.
         *
         * @var    string  $option
         * @param  string  $value
         * @param  string  $deprecated
         * @param  string  $autoload
         */
        do_action('ol.zeus.option_add_before_'.$option, $value, $deprecated, $autoload);

        add_option($option, $value, $deprecated, $autoload);

        /**
         * Fires after adding option into database.
         *
         * @var    string  $option
         * @param  string  $value
         * @param  string  $deprecated
         * @param  string  $autoload
         */
        do_action('ol.zeus.option_add_after_'.$option, $value, $deprecated, $autoload);
    }

    /**
     * Set a value into options
     *
     * @param  string  $option
     */
    public static function delete($option) : void
    {
        /**
         * Fires before deleting option from database.
         *
         * @var    string  $option
         */
        do_action('ol.zeus.option_delete_before_'.$option);

        delete_option($option);

        /**
         * Fires after deleting option from database.
         *
         * @var    string  $option
         */
        do_action('ol.zeus.option_delete_after_'.$option);
    }

    /**
     * Return a value from options
     *
     * @param  string  $option
     * @param  string  $default
     * @param  string  $item
     *
     * @return mixed
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
         *
         * @return array
         */
        $value = apply_filters('ol.zeus.option_get_'.$option, $value);

        // Return value
        return is_array($value) && isset($value[$item]) ? $value[$item] : $value;
    }

    /**
     * Set a value into options
     *
     * @param  string  $option
     * @param  string  $value
     * @param  string  $type
     * @param  int     $id
     */
    public static function set($option, $value, $type = '', $id = 0) : void
    {
        /**
         * Works on option's value.
         *
         * @var    string  $option
         * @param  mixed   $value
         *
         * @return array
         */
        $value = apply_filters('ol.zeus.option_set_'.$option, $value);

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
    public static function update($option, $value) : void
    {
        /**
         * Fires before updating option in database.
         *
         * @var    string  $option
         * @param  string  $value
         */
        do_action('ol.zeus.option_update_before_'.$option, $value);

        update_option($option, $value);

        /**
         * Fires after updating option in database.
         *
         * @var    string  $option
         * @param  string  $value
         */
        do_action('ol.zeus.option_update_after_'.$option, $value);
    }

    /**
     * Clean details on value
     *
     * @param  mixed   $value
     *
     * @return mixed
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
     * Get a value from admin page options
     *
     * @param  string  $admin_id
     * @param  string  $option
     * @param  string  $default
     *
     * @return mixed
     */
    public static function getAdminOption($admin_id, $option = '', $default = '')
    {
        $values = self::get($admin_id, []);
        $value  = empty($option) ? $values : (isset($values[$option]) ? $values[$option] : $default);

        /**
         * Works on admin page's option value.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  string  $admin_id
         *
         * @return mixed
         */
        return apply_filters('ol.zeus.option_get_admin_page_'.$option, $value);
    }

    /**
     * Force update a value into user options
     *
     * @param  string  $admin_id
     * @param  string  $option
     * @param  mixed   $value
     */
    public static function updateAdminOption($admin_id, $option, $value) : void
    {
        /**
         * Fires before updating admin page's option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  string  $admin_id
         */
        do_action('ol.zeus.option_update_admin_page_before_'.$option, $value, $admin_id);

        $values = self::getAdminOption($admin_id, []);
        $values[$option] = $value;

        self::add($admin_id, $values, []);

        /**
         * Fires after updating admin page's option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  string  $admin_id
         */
        do_action('ol.zeus.option_update_admin_page_after_'.$option, $value, $admin_id);
    }

    /**
     * Get a value from user options
     *
     * @param  string  $user_id
     * @param  string  $option
     *
     * @return mixed
     */
    public static function getAuthorMeta($user_id, $option)
    {
        $value = get_the_author_meta($option, $user_id);

        /**
         * Works on author's meta option value.
         *
         * @var    string  $option
         * @param  mixed   $value
         *
         * @return mixed
         */
        return apply_filters('ol.zeus.option_get_author_meta_'.$option, $value);
    }

    /**
     * Force update a value into user options
     *
     * @param  string  $user_id
     * @param  string  $option
     * @param  mixed   $value
     */
    public static function updateAuthorMeta($user_id, $option, $value) : void
    {
        /**
         * Fires before updating user's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $user_id
         */
        do_action('ol.zeus.option_update_user_meta_before_'.$option, $value, $user_id);

        update_user_meta($user_id, $option, $value);

        /**
         * Fires after updating user's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $user_id
         */
        do_action('ol.zeus.option_update_user_meta_after_'.$option, $value, $user_id);
    }

    /**
     * Get a value from post options
     *
     * @param  string  $post_id
     * @param  string  $option
     *
     * @return mixed
     */
    public static function getPostMeta($post_id, $option)
    {
        $value = get_post_meta($post_id, $option, true);

        /**
         * Works on post's meta option value.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $post_id
         *
         * @return mixed
         */
        return apply_filters('ol.zeus.option_get_post_meta_'.$option, $value, $post_id);
    }

    /**
     * Force update a value into post options
     *
     * @param  string  $post_id
     * @param  string  $option
     * @param  mixed   $value
     */
    public static function updatePostMeta($post_id, $option, $value) : void
    {
        /**
         * Fires before updating post's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $post_id
         */
        do_action('ol.zeus.option_update_post_meta_before_'.$option, $value, $post_id);

        update_post_meta($post_id, $option, $value);

        /**
         * Fires after updating post's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $post_id
         */
        do_action('ol.zeus.option_update_post_meta_after_'.$option, $value, $post_id);
    }

    /**
     * Get a value from term options
     *
     * @param  string  $term_id
     * @param  string  $option
     * @param  mixed   $default
     *
     * @return mixed
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

        /**
         * Works on term's meta option value.
         *
         * @var    string  $option
         * @param  mixed   $value
         *
         * @return mixed
         */
        return apply_filters('ol.zeus.option_get_term_meta_'.$option, $value);
    }

    /**
     * Force update a value into term options
     *
     * @param  string  $term_id
     * @param  string  $option
     * @param  mixed   $value
     */
    public static function updateTermMeta($term_id, $option, $value) : void
    {
        /**
         * Fires before updating term's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $term_id
         */
        do_action('ol.zeus.option_update_term_meta_before_'.$option, $value, $term_id);

        update_term_meta($term_id, $option, $value);

        /**
         * Fires after updating term's meta option in database.
         *
         * @var    string  $option
         * @param  mixed   $value
         * @param  int     $term_id
         */
        do_action('ol.zeus.option_update_term_meta_after_'.$option, $value, $term_id);
    }
}
