<?php

namespace GetOlympus\Hera\Notification\Controller;

/**
 * Displays admin messages and notifications when its needed.
 *
 * @package Olympus Hera
 * @subpackage Notification\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Notification
{
    /**
     * Constructor.
     */
    public function __construct(){}

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $content
     */
    public static function error($content = '')
    {
        // Check if we are in admin panel
        if (!OLH_ISADMIN || empty($content)) {
            return;
        }

        // Set error on template main page
        add_filter('olh_template_error', function ($notice) use ($content) {
            $notice[] = ['error', $content];
            return $notice;
        }, 10, 1);
    }
}
