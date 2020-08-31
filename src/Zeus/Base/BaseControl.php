<?php

namespace GetOlympus\Zeus\Base;

use GetOlympus\Zeus\Base\BaseTrait;

/**
 * Base Control controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.2
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

if (!class_exists('WP_Customize_Control')) {
    include_once ABSPATH.'wp-includes'.S.'class-wp-customize-control.php';
}

class BaseControl extends \WP_Customize_Control
{
    use BaseTrait;

    /**
     * Gets the value of instance.
     *
     * @return self
     */
    public static function getInstance() : self
    {
        return new static(false, false, []);
    }
}
