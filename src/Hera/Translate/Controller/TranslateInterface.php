<?php

namespace GetOlympus\Hera\Translate\Controller;

/**
 * Translate interface.
 *
 * @package Olympus Hera
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
     * Noop typo.
     *
     * @param string $singular
     * @param string $plural
     * @return string
     */
    public static function n($singular, $plural);

    /**
     * Translate typo.
     *
     * @param   string  $content
     * @param   array   $args
     * @param   string  $alias
     * @return  Translate
     */
    public static function t($content, $args = [], $alias = 'core');
}
