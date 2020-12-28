<?php

namespace GetOlympus\Zeus\Base;

use GetOlympus\Zeus\Base\BaseTrait;

/**
 * Base Section controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.8
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

if (!class_exists('WP_Customize_Section')) {
    include_once ABSPATH.'wp-includes'.S.'class-wp-customize-section.php';
}

class BaseSection extends \WP_Customize_Section
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
