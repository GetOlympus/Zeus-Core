<?php

namespace GetOlympus\Zeus\Utils;

use GetOlympus\Hermes\Hermes;

/**
 * Translates typos.
 *
 * @package    OlympusZeusCore
 * @subpackage Utils
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Translate extends Hermes
{
    /**
     * Load translations.
     *
     * @param  array   $translations
     * @param  string  $locale
     */
    public static function l($translations = [], $locale = 'default') : void
    {
        parent::l($translations, $locale);
    }

    /**
     * Noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @param  integer $number
     * @param  string  $domain
     *
     * @return string
     */
    public static function n($single, $plural, $number = 1, $domain = 'olympus-zeus') : string
    {
        return parent::n($single, $plural, $number, $domain);
    }

    /**
     * Prepare noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @param  string  $domain
     *
     * @return string
     */
    public static function noop($single, $plural, $domain = 'olympus-zeus') : string
    {
        return parent::noop($single, $plural, $domain);
    }

    /**
     * Translate typo.
     *
     * @param  string  $message
     * @param  string  $domain
     *
     * @return string
     */
    public static function t($message, $domain = 'olympus-zeus') : string
    {
        return parent::t($message, $domain);
    }
}
