<?php

namespace GetOlympus\Zeus\Base;

use GetOlympus\Zeus\Base\BaseTrait;

/**
 * Base Widget controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.8
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

if (!class_exists('WP_Widget')) {
    require_once ABSPATH.'wp-includes'.S.'class-wp-widget.php';
}

class BaseWidget extends \WP_Widget
{
    use BaseTrait;
}
