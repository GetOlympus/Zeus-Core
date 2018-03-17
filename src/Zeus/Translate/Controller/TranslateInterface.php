<?php

namespace GetOlympus\Zeus\Translate\Controller;

/**
 * Translate interface.
 *
 * @package Olympus Zeus-Core
 * @subpackage Translate\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface TranslateInterface
{
    /**
     * Get singleton.
     */
    public static function getInstance();

    /**
     * Choice typo.
     *
     * @param   string  $message
     * @param   integer $number
     * @param   array   $args
     * @param   string  $domain
     * @param   string  $locale
     * @return  string
     */
    public static function c($message, $number, $args = [], $domain = 'core', $locale = 'en_EN');

    /**
     * Noop typo from WordPress.
     *
     * @param   string $singular
     * @param   string $plural
     * @return  string
     */
    public static function n($singular, $plural);

    /**
     * Translate typo.
     *
     * @param   string  $message
     * @param   array   $args
     * @param   string  $domain
     * @param   string  $locale
     * @return  Translate
     */
    public static function t($message, $args = [], $domain = 'core', $locale = 'en_EN');
}
