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
     * Return request value.
     *
     * @param string $param
     */
    public static function post($param, $default = '');
}
