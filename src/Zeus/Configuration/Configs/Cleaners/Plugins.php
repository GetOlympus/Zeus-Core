<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners;

use GetOlympus\Zeus\Configuration\Configs\Cleaners\Cleaner;

/**
 * Plugins cleaner
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Plugins extends Cleaner
{
    /**
     * @var string
     */
    protected $append = 'Plugin';

    /**
     * @var array
     */
    protected $available = [
        /**
         * @see GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins for more details
         */
        'bbpress'             => true,
        'contact-form'        => true,
        'google-tag-manager'  => true,
        'gravity-form'        => true,
        'jetpack'             => true,
        'onesignal'           => true,
        'the-events-calendar' => true,
        'w3tc'                => true,
        'woocommerce'         => true,
        'wp-rocket'           => true,
        'wp-socializer'       => true,
        'yarpp'               => true,
        'yoast'               => true,
    ];

    /**
     * @var string
     */
    protected $classname = 'GetOlympus\\Zeus\\Configuration\\Configs\\Cleaners\\Plugins\\';
}
