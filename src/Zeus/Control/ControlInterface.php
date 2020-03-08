<?php

namespace GetOlympus\Zeus\Control;

/**
 * Control interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Control
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.2
 *
 */

interface ControlInterface
{
    /**
     * Enqueue scripts and styles.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function copyFile($path) : string;

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue() : void;

    /**
     * Render Control in the customizer.
     *
     * @throws ControlException
     */
    public function render_content() : void;

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     */
    public function to_json() : void;

    /**
     * Retrieve Control translations
     *
     * @throws ControlException
     *
     * @return array
     */
    public static function translate() : array;

    /**
     * Array sanitization
     *
     * @param  string  $input
     *
     * @return array
     */
    public static function sanitize_array($input);

    /**
     * Color sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_color($input, $setting);

    /**
     * Date time sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_datetime($input, $setting);

    /**
     * Google fonts sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_googlefonts($input);

    /**
     * Integer sanitization
     *
     * @param  string  $input
     *
     * @return int
     */
    public static function sanitize_integer($input);

    /**
     * Radio sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return mixed
     */
    public static function sanitize_radio($input, $setting);

    /**
     * Range sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_range($input, $setting);

    /**
     * Text sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_text($input);

    /**
     * Toggle sanitization
     *
     * @param  string  $input
     *
     * @return bool
     */
    public static function sanitize_toggle($input);

    /**
     * URL sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_url($input);

    /**
     * Set number in specified range
     *
     * @param  number  $number
     * @param  number  $min
     * @param  number  $max
     *
     * @return number
     */
    public static function set_in_range($number, $min, $max);
}
