<?php

namespace GetOlympus\Zeus\Base;

use GetOlympus\Zeus\Base\BaseTrait;

/**
 * Base Walker controller
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

if (!class_exists('Walker_Nav_Menu')) {
    include_once ABSPATH.'wp-includes'.S.'class-walker-nav-menu.php';
}

class BaseWalker extends \Walker_Nav_Menu
{
    use BaseTrait;
}
