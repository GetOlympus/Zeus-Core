<?php

namespace GetOlympus\Zeus\Control;

use GetOlympus\Zeus\Base\BaseControl;
use GetOlympus\Zeus\Control\ControlException;
use GetOlympus\Zeus\Utils\Helpers;

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

abstract class Control extends BaseControl
{
    /**
     * @var array
     */
    public static $scripts = [];

    /**
     * @var array
     */
    public static $styles = [];

    /**
     * @var string
     */
    protected $textdomain = 'zeuscontrol';

    /**
     * @var string
     */
    public $type = 'zeus-control';

    /**
     * Enqueue scripts and styles.
     */
    public static function assets() : void
    {
        // Get instance
        try {
            $control = self::getInstance();
        } catch (Exception $e) {
            throw new ControlException(Translate::t('control.errors.class_is_not_defined'));
        }

        $defaults = [
            'src'        => '',
            'deps'       => [],
            'ver'        => false,
            'in_footer'  => true,
            'media'      => 'all',
        ];

        foreach (['js' => static::$scripts, 'css' => static::$styles] as $type => $files) {
            if (empty($files)) {
                continue;
            }

            foreach ($files as $key => $opts) {
                $opts = array_merge($defaults, $opts);

                $func = 'js' === $type ? 'wp_enqueue_script' : 'wp_enqueue_style';
                $src  = self::copyFile($opts['src'], $type);
                $last = 'js' === $type ? $opts['in_footer'] : $opts['media'];

                $func($key, $src, $opts['deps'], $opts['ver'], $last);
            }
        }
    }

    /**
     * Enqueue scripts and styles.
     *
     * @param  string  $path
     * @param  string  $folder
     *
     * @return string
     */
    public static function copyFile($path, $folder) : string
    {
        // Update details
        $basename = basename($path);
        $source   = rtrim(dirname($path), S);
        $target   = rtrim(OL_ZEUS_DISTPATH, S).S.$folder;

        // Update file path on dist accessible folder
        Helpers::copyFile($source, $target, $basename);

        // Return file uri
        return esc_url(OL_ZEUS_URI.$folder.S.$basename);
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
}
