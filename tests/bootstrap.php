<?php

/**
 * OLYMPUS ZEUS CORE BOOTSTRAP TESTS.
 *
 * @package Olympus Zeus-Core
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.46
 *
 */

/**
 * Directory separator
 */

defined('S') or define('S', DIRECTORY_SEPARATOR);

/**
 * Paths
 */

// Nothing for now.

/**
 * Composer autoload
 */

require dirname(__FILE__).S.'..'.S.'vendor'.S.'autoload.php';
require dirname(__FILE__).S.'helpers.php';
