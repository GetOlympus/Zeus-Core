<?php

namespace GetOlympus\Zeus\Control;

use GetOlympus\Zeus\Base\BaseControl;
use GetOlympus\Zeus\Control\ControlException;
use GetOlympus\Zeus\Control\ControlInterface;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Abstract class to define all Control context with authorized controls, how to
 * write some functions and every usefull checks.
 *
 * @package    OlympusZeusCore
 * @subpackage Control
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.2
 *
 */

abstract class Control extends BaseControl implements ControlInterface
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $textdomain = 'zeuscontrol';

    /**
     * @var string
     */
    public $type;

    /**
     * @var object
     */
    public $value;

    /**
     * Constructor
     */
    public function __construct($manager, $id, $args = [], $options = [])
    {
        parent::__construct($manager, $id, $args);

        // Set type
        $words = preg_split('/(?=[A-Z])/', $this->getClass()['name']);
        $this->type = ltrim(implode('_', $words), '_');
    }

    /**
     * Enqueue scripts and styles.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function copyFile($path) : string
    {
        $basename = basename($path);

        // Update details
        $fileuri  = OL_ZEUS_URI.'js'.S.$basename;
        $source   = rtrim(dirname($path), S);
        $target   = rtrim(OL_ZEUS_DISTPATH, S).S.'js';

        // Update file path on dist accessible folder
        Helpers::copyFile($source, $target, $basename);

        // Return file uri
        return esc_url($fileuri);
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue() : void
    {
        $details = [];

        // Retrieve path to assets Resources and shortname's class
        $class = $this->getClass();
        $path  = dirname(dirname($class['resources'])).S.'assets'.S;
        $key   = Helpers::urlize(strtolower($class['name']));

        if (!empty($this->scripts)) {
            $num = 1;

            foreach ($this->scripts as $script) {
                wp_enqueue_script($key.'-'.$num, $this->copyFile($path.$script), ['jquery'], false, true);
                $num++;
            }
        }

        if (!empty($this->styles)) {
            $num = 1;

            foreach ($this->styles as $style) {
                wp_enqueue_style($key.'-'.$num, $this->copyFile($path.$style), [], false, 'all');
                $num++;
            }
        }
    }

    /**
     * Render Control in the customizer.
     *
     * @throws ControlException
     */
    public function render_content() : void // phpcs:ignore
    {
        // Check type
        if (empty($this->type)) {
            throw new ControlException(Translate::t('control.errors.type_is_not_defined'));
        }

        // Check template
        if (empty($this->template)) {
            throw new ControlException(Translate::t('control.errors.template_is_not_defined'));
        }

        $class = $this->getClass();

        // Works on context
        $this->context = empty($this->context) ? strtolower($class['name']) : $this->context;

        // Set value
        $this->value = $this->value();
        $this->value = is_string($this->value) ? stripslashes($this->value) : $this->value;

        // Set vars
        $this->vars = $this->getVars($this->value);

        // Update value
        $this->value = isset($this->vars['value']) ? $this->vars['value'] : $this->value;

        // Define vars
        $vars = [
            'control_path'  => $class['resources'].S.'views',
            'template_path' => '@core/controls/base.html.twig',

            'description'   => $this->description,
            'choices'       => $this->choices,
            'default'       => $this->default,
            'id'            => $this->id,
            'include_time'  => $this->include_time,
            'input_attrs'   => $this->input_attrs,
            'label'         => $this->label,
            'link'          => $this->get_link(),
            'value'         => $this->value,
        ];

        // Add custom vars
        $vars = array_merge($vars, $this->vars);

        // Render view
        $render = new Render($this->context, $this->template, $vars);
        $render->view();
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     */
    public function to_json() : void // phpcs:ignore
    {
        parent::to_json();

        // Update JSON with vars
        if (!empty($this->vars)) {
            foreach ($this->vars as $k => $value) {
                $this->json[$k] = $value;
            }
        }
    }

    /**
     * Retrieve Control translations
     *
     * @throws ControlException
     *
     * @return array
     */
    public static function translate() : array
    {
        // Get instance
        try {
            $control = self::getInstance();
        } catch (Exception $e) {
            throw new ControlException(Translate::t('control.errors.class_is_not_defined'));
        }

        // Set translations
        $class = $control->getClass();

        return [
            $control->textdomain => dirname(dirname($class['resources'])).S.'languages'
        ];
    }

    /**
     * Array sanitization
     *
     * @param  string  $input
     *
     * @return array
     */
    public static function sanitize_array($input) // phpcs:ignore
    {
        // Check value
        $input = self::sanitize_text($input);

        return !is_array($input) ? [$input] : $input;
    }

    /**
     * Color sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return string
     */
    public static function sanitize_color($input, $setting) // phpcs:ignore
    {
        // Check input
        if (empty($input) || is_array($input)) {
            return $setting->default;
        }

        // Check rgba() format
        if (false === strpos($input, 'rgba')) {
            return sanitize_hex_color($input);
        }

        $input = str_replace(' ', '', $input);
        sscanf($input, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);

        $input = 'rgba(';
        $input .= self::set_in_range($red, 0, 255).',';
        $input .= self::set_in_range($green, 0, 255).',';
        $input .= self::set_in_range($blue, 0, 255).',';
        $input .= self::set_in_range($alpha, 0, 1);
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
    public static function sanitize_datetime($input, $setting) // phpcs:ignore
    {
        // Set date format
        $format = $setting->manager->get_control($setting->id)->include_time ? 'Y-m-d H:i:s' : 'Y-m-d';

        // Define date
        $date = DateTime::createFromFormat($format, $input);

        return !$date ? DateTime::createFromFormat($format, $setting->default) : $date->format($format);
    }

    /**
     * Google fonts sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_googlefonts($input) // phpcs:ignore
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
     * Integer sanitization
     *
     * @param  string  $input
     *
     * @return int
     */
    public static function sanitize_integer($input) // phpcs:ignore
    {
        // Check value
        return (int) $input;
    }

    /**
     * Radio sanitization
     *
     * @param  string  $input
     * @param  object  $setting
     *
     * @return mixed
     */
    public static function sanitize_radio($input, $setting) // phpcs:ignore
    {
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
    public static function sanitize_range($input, $setting) // phpcs:ignore
    {
        // Set attributes
        $input_attrs = $setting->manager->get_control($setting->id)->input_attrs;

        // Define extrema
        $min    = isset($input_attrs['min']) ? $input_attrs['min'] : $input;
        $max    = isset($input_attrs['max']) ? $input_attrs['max'] : $input;
        $step   = isset($input_attrs['step']) ? $input_attrs['step'] : 1;
        $number = floor($input / $input_attrs['step']) * $input_attrs['step'];

        return self::set_in_range($number, $min, $max);
    }

    /**
     * Text sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_text($input) // phpcs:ignore
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
     * Toggle sanitization
     *
     * @param  string  $input
     *
     * @return bool
     */
    public static function sanitize_toggle($input) // phpcs:ignore
    {
        // Check value
        return true === $input ? 1 : 0;
    }

    /**
     * URL sanitization
     *
     * @param  string  $input
     *
     * @return string
     */
    public static function sanitize_url($input) // phpcs:ignore
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
    public static function set_in_range($number, $min, $max) // phpcs:ignore
    {
        // Check right number
        return $input < $min ? $min : ($input > $max ? $max : $input);
    }

    /**
     * Prepare variables.
     *
     * @param  object  $value
     *
     * @return array
     */
    abstract public function getVars($value);
}
