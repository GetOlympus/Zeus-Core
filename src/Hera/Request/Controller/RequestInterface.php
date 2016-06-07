<?php

namespace GetOlympus\Hera\Request\Controller;

/**
 * Request interface.
 *
 * @package Olympus Hera
 * @subpackage Request\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface RequestInterface
{
    /**
     * Return request value.
     *
     * @param string $param
     */
    public static function get($param, $default = '');

    /**
     * Get used slug in current admin panel page.
     *
     * @return string $slug
     */
    public static function getCurrentSlug();

    /**
     * Return request value.
     *
     * @param string $param
     */
    public static function post($param, $default = '');
}
