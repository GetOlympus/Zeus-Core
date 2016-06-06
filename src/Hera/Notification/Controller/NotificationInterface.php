<?php

namespace GetOlympus\Hera\Notification\Controller;

/**
 * Notification interface.
 *
 * @package Olympus Hera
 * @subpackage Notification\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface NotificationInterface
{
    /**
     * Error display.
     *
     * @param string $content
     */
    public static function error($content = '');
}
