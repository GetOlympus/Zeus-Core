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
    public function render_content() : void; // phpcs:ignore

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     */
    public function to_json() : void; // phpcs:ignore

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
    public static function sanitize_array($input); // phpcs:ignore

    /**
     * Color sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_color($input, $setting); // phpcs:ignore

    /**
     * Date time sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_datetime($input, $setting); // phpcs:ignore

    /**
     * Google fonts sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_googlefonts($input); // phpcs:ignore

    /**
     * Integer sanitization
     *
     * @param  string  $input
     *
     * @return int
     */
    public static function sanitize_integer($input); // phpcs:ignore

    /**
     * Radio sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return mixed
     */
    public static function sanitize_radio($input, $setting); // phpcs:ignore

    /**
     * Range sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_range($input, $setting); // phpcs:ignore

    /**
     * Text sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_text($input); // phpcs:ignore

    /**
     * Toggle sanitization
     *
     * @param  string  $input
     *
     * @return bool
     */
    public static function sanitize_toggle($input); // phpcs:ignore

    /**
     * URL sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_url($input); // phpcs:ignore

    /**
     * Set number in specified range
     *
     * @param  number  $number
     * @param  number  $min
     * @param  number  $max
     *
     * @return number
     */
    public static function set_in_range($number, $min, $max); // phpcs:ignore
}
