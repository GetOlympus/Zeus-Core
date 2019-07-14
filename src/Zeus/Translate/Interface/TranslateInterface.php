<?php

namespace GetOlympus\Zeus\Translate\Interface;

/**
 * Translate interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Translate\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface TranslateInterface
{
    /**
     * Get singleton.
     */
    public static function getInstance();

    /**
     * Load translations.
     *
     * @param  array   $translations
     * @param  string  $locale
     */
    public static function l($translations = [], $locale = 'default');

    /**
     * Noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @param  integer $number
     * @param  string  $domain
     * @return string
     */
    public static function n($single, $plural, $number = 1, $domain = 'olympus-zeus');

    /**
     * Prepare noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @return string
     */
    public static function noop($single, $plural);

    /**
     * Translate typo.
     *
     * @param  string  $message
     * @param  string  $domain
     * @return string
     */
    public static function t($message, $domain = 'olympus-zeus');
}
