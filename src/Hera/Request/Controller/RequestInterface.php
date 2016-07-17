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
     * Return a slug list where it is authorized to render assets.
     *
     * @return  array $authorizedPage
     */
    public static function authorizedAssets();

    /**
     * Return $_GET value.
     *
     * @param   string $param
     * @param   string $default
     * @return  string $value
     */
    public static function get($param, $default = '');

    /**
     * Get used slug in current admin panel page.
     *
     * @return string $slug
     */
    public static function getCurrentSlug();

    /**
     * Return $_POST value.
     *
     * @param   string $param
     * @param   string $default
     * @return  string $value
     */
    public static function post($param, $default = '');
}
