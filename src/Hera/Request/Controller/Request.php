<?php

namespace GetOlympus\Hera\Request\Controller;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Displays admin messages and notifications when its needed.
 *
 * @package Olympus Hera
 * @subpackage Request\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Request
{
    /**
     * Constructor.
     */
    public function __construct(){}

    /**
     * Return request value.
     *
     * @param string $param
     */
    public static function get($param, $default = '')
    {
        return SymfonyRequest::createFromGlobals()->query->get($param, $default);
    }
}
