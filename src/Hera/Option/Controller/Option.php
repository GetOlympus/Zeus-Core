<?php

namespace GetOlympus\Hera\Option\Controller;

/**
 * Works with WP options.
 *
 * @package Olympus Hera
 * @subpackage Option\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Option
{
    /**
     * Constructor.
     */
    public function __construct(){}

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
     * @return mixed|string|void
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
         * @var string $option
         * @param array $value
         * @return array $value
         */
        $value = apply_filters('olh_option_get_'.$option, $value);

        // Return value
        return is_array($value) && isset($value[$item]) ? $value[$item] : $value;
    }

    /**
     * Force update a value into term options without transient
     *
     * @param string $termId
     * @param string $option
     * @param boolean $multiple
     * @return string|array|boolean|null $multiple
     */
    public static function getTermMeta($termId, $option, $multiple = false)
    {
        return get_term_meta($termId, $option, $multiple);
    }

    /**
     * Retrieve field value
     *
     * @param array $details
     * @param object $default
     * @param string $id
     * @param boolean $multiple
     */
    public static function getFieldValue($details, $default, $id = '', $multiple = false)
    {
        // Build details
        $post = isset($details['post']) ? $details['post'] : 0;
        $prefix = isset($details['prefix']) ? $details['prefix'] : '';
        $termid = isset($details['term_id']) ? $details['term_id'] : 0;
        $structure = isset($details['structure']) ? $details['structure'] : '';
        $widgetValue = isset($details['widget_value']) ? $details['widget_value'] : '';

        // Post types
        if (!empty($post)) {
            $value = get_post_meta($post->ID, $post->post_type.'-'.$id, !$multiple);
            $value = empty($value) ? $default : $value;
        }
        // Special settings
        else if (preg_match('/^olz-configs-/', $id)) {
            // Update option from olz_configs_frontend_login into frontend_login
            $option = $prefix.$id;
            $id = str_replace('olz-configs-', '', $id);

            // Check id[suboption]
            if (preg_match('/\[.*\]/', $id)) {
                // Get option
                $option = substr($id, 0, strpos($id,'['));

                // Get suboption
                $suboption = substr($id, strpos($id,'['));
                $suboption = str_replace(['[', ']'], '', $suboption);

                // Get value
                $vals = self::get('olz-config', [], $option);
                $value = !$vals ? $default : (isset($vals[$suboption]) ? $vals[$suboption] : $default);
            }
            else {
                // Get value
                $value = self::get('olz-config', [], $id);
                $value = !$value ? $default : $value;
            }
        }
        // WP 4.4
        else if (function_exists('get_term_meta') && !empty($prefix) && !empty($termid)) {
            $value = get_term_meta($termid, $prefix.'-'.$id, true);
            $value = !$value ? $default : $value;
        }
        // Default
        else {
            $option = !empty($prefix) ? str_replace(['%TERM%', '%SLUG%'], [$prefix, $id], $structure) : $id;
            $value = !empty($widgetValue) ? $widgetValue : self::get($option, $default);
        }

        // Strip slasches?
        return $multiple || is_array($value) ? $value : stripslashes($value);
    }

    /**
     * Set a value into options
     *
     * @param string $option
     * @param string $value
     */
    public static function set($option, $value)
    {
        /**
         * Works on option's value.
         *
         * @var string $option
         * @param array $value
         * @return array $value
         */
        $value = apply_filters('olh_option_set_'.$option, $value);

        // Set value into DB without autoload
        if (false === get_option($option)) {
            self::add($option, $value);
        }
        else {
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
     * Force update a value into term options without transient
     *
     * @param string $termId
     * @param string $option
     * @param string $value
     */
    public static function updateTermMeta($termId, $option, $value)
    {
        update_term_meta($termId, $option, $value);
    }
}
