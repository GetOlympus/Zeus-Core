<?php

namespace GetOlympus\Zeus\Base;

/**
 * Base trait
 *
 * @package    OlympusZeusCore
 * @subpackage Base
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.8
 *
 */

trait BaseTrait
{
    /**
     * @var mixed
     */
    protected $model;

    /**
     * Retrieve class details.
     *
     * @return array
     */
    protected function getClass() : array
    {
        // Retrieve path to Resources and shortname's class
        $class = new \ReflectionClass(get_class($this));

        // Return a simple array
        return [
            'name'      => $class->getShortName(),
            'path'      => $class->getFileName(),
            'resources' => dirname(dirname($class->getFileName())).S.'Resources',
        ];
    }

    /**
     * Gets the value of instance.
     *
     * @return static
     */
    public static function getInstance() : self
    {
        return new static();
    }

    /**
     * Gets the model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Array sanitization
     *
     * @param  string  $input
     *
     * @return array
     */
    public static function zeusSanitizeArray($input)
    {
        // Check value
        $input = self::zeusSanitizeText($input);

        return !is_array($input) ? [$input] : $input;
    }

    /**
     * Checkbox sanitization
     *
     * @param  bool    $input
     *
     * @return bool
     */
    public static function zeusSanitizeCheckbox($input)
    {
        // Check status
        //return 1 === absint($input) ? 1 : 0;
        return isset($input) && true == $input;
    }

    /**
     * Color sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeColor($input, $setting)
    {
        // Check input
        if (empty($input)) {
            return $setting->default;
        }

        // Check rgba() format
        if (false === strpos($input, 'rgba')) {
            return sanitize_hex_color($input);
        }

        $input = str_replace(' ', '', $input);
        sscanf($input, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);

        $input = 'rgba(';
        $input .= self::zeusSetInRange($red, 0, 255).',';
        $input .= self::zeusSetInRange($green, 0, 255).',';
        $input .= self::zeusSetInRange($blue, 0, 255).',';
        $input .= self::zeusSetInRange($alpha, 0, 1);
        $input .= ')';

        return $input;
    }

    /**
     * Date time sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeDatetime($input, $setting)
    {
        // Set date format
        $format = $setting->manager->get_control($setting->id)->include_time ? 'Y-m-d H:i:s' : 'Y-m-d';

        // Define date
        $date = DateTime::createFromFormat($format, $input);

        return !$date ? DateTime::createFromFormat($format, $setting->default) : $date->format($format);
    }

    /**
     * Dropdown pages sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeDropdownPages($input, $setting)
    {
        // Integer format & page status
        $input = absint($input);
        $status = get_post_status($input);

        return 'publish' === $status ? $input : $setting->default;
    }

    /**
     * File sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     * @param  array   $mimetypes
     *
     * @return string
     */
    public static function zeusSanitizeFile($input, $setting, $mimetypes = [])
    {
        // Allowed file types
        $mimetypes = !empty($mimetypes) ? $mimetypes : [
            'gif'          => 'image/gif',
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
        ];

        // Check filetype from filename
        $filename = wp_check_filetype($input, $mimetypes);

        return $filename['ext'] ? $input : $setting->default;
    }

    /**
     * Float sanitization
     *
     * @param  string  $input
     *
     * @return float
     */
    public static function zeusSanitizeFloat($input)
    {
        // Filter_var value
        return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Google fonts sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeGooglefonts($input)
    {
        // Decode input
        $input = json_decode($input, true);

        // Array case
        if (is_array($input)) {
            foreach ($input as $k => $value) {
                $input[$k] = sanitize_text_field($value);
            }

            return json_encode($input);
        }

        return json_encode(sanitize_text_field($input));
    }

    /**
     * HTML sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeHtml($input)
    {
        global $allowedposttags;

        return wp_kses($input, $allowedposttags);
    }

    /**
     * Image sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeImage($input, $setting)
    {
        // Allowed file types
        $mimetypes = [
            'bmp'          => 'image/bmp',
            'gif'          => 'image/gif',
            'ico'          => 'image/x-icon',
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
            'tif|tiff'     => 'image/tiff',
        ];

        return esc_url_raw(self::zeusSanitizeFile($input, $setting, $mimetypes));
    }

    /**
     * Integer sanitization
     *
     * @param  string  $input
     *
     * @return int
     */
    public static function zeusSanitizeInteger($input)
    {
        // Cast value
        return (int) $input;
    }

    /**
     * Javascript sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeJavascript($input)
    {
        // Base64 value
        return base64_encode($input);
    }

    /**
     * Javascript for output sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeJavascriptOutput($input)
    {
        // Escape value
        return esc_textarea(self::zeusSanitizeJavascript($input));
    }

    /**
     * Multi check sanitization
     *
     * @param  array   $input
     *
     * @return array
     */
    public static function zeusSanitizeMulticheck($input, $setting)
    {
        // Check values
        $values = !is_array($input) ? explode(',', $input) : $input;

        return !empty($values) ? array_map('sanitize_text_field', $values) : [];
    }

    /**
     * Multi choices sanitization
     *
     * @param  array   $input
     * @param  object  $setting
     *
     * @return array
     */
    public static function zeusSanitizeMultichoices($input, $setting)
    {
        $temp = self::zeusSanitizeArray($input);

        // Retrieve list choices
        $choices = $setting->manager->get_control($setting->id)->choices;

        // Iterate on input choices to remove unused
        foreach ($temp as $key => $value) {
            if (array_key_exists($value, $choices)) {
                continue;
            }

            unset($input[$key]);
        }

        return is_array($input) ? $input : $setting->default;
    }

    /**
     * No HTML sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeNoHtml($input)
    {
        // Remove HTML
        return wp_filter_nohtml_kses($input);
    }

    /**
     * Number sanitization
     *
     * @param  string  $input
     *
     * @return int
     */
    public static function zeusSanitizeNumber($input)
    {
        // Cast value
        return is_numeric($input) ? (int) $input : 0;
    }

    /**
     * Number to blank sanitization
     *
     * @param  string  $input
     *
     * @return mixed
     */
    public static function zeusSanitizeNumberBlank($input)
    {
        // Cast value
        return is_numeric($input) ? (int) $input : '';
    }

    /**
     * Radio sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeRadio($input, $setting)
    {
        // Slug format
        $input = sanitize_key($input);

        // Retrieve list choices
        $choices = $setting->manager->get_control($setting->id)->choices;

        return array_key_exists($input, $choices) ? $input : $setting->default;
    }

    /**
     * Range sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeRange($input, $setting)
    {
        // Set attributes
        $attrs = $setting->manager->get_control($setting->id)->input_attrs;

        // Define extrema
        $min    = isset($attrs['min']) ? $attrs['min'] : $input;
        $max    = isset($attrs['max']) ? $attrs['max'] : $input;
        $step   = isset($attrs['step']) ? $attrs['step'] : 1;
        $number = floor($input / $step) * $step;

        return self::zeusSetInRange($number, $min, $max);
    }

    /**
     * Select sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function zeusSanitizeSelect($input, $setting)
    {
        // Slug format
        $input = sanitize_key($input);

        // Retrieve list choices
        $choices = $setting->manager->get_control($setting->id)->choices;

        return array_key_exists($input, $choices) ? $input : $setting->default;
    }

    /**
     * Text sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeText($input)
    {
        // Check input
        $input = false !== strpos($input, ',') ? explode(',', $input) : $input;

        // Array case
        if (is_array($input)) {
            foreach ($input as $k => $value) {
                $input[$k] = sanitize_text_field($value);
            }

            return implode(',', $input);
        }

        return sanitize_text_field($input);
    }

    /**
     * Textarea sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeTextarea($input)
    {
        // Escape value
        return esc_textarea($input);
    }

    /**
     * Toggle sanitization
     *
     * @param  string  $input
     *
     * @return bool
     */
    public static function zeusSanitizeToggle($input)
    {
        // Check status
        return true === $input ? 1 : 0;
    }

    /**
     * URL sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function zeusSanitizeUrl($input)
    {
        // Check input
        $input = false !== strpos($input, ',') ? explode(',', $input) : $input;

        // Array case
        if (is_array($input)) {
            foreach ($input as $k => $value) {
                $input[$k] = esc_url_raw($value);
            }

            return implode(',', $input);
        }

        return esc_url_raw($input);
    }

    /**
     * Set number in specified range
     *
     * @param  number  $number
     * @param  number  $min
     * @param  number  $max
     *
     * @return number
     */
    public static function zeusSetInRange($number, $min, $max)
    {
        // Check right number
        return $number < $min ? $min : ($number > $max ? $max : $number);
    }
}
