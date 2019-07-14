<?php

namespace GetOlympus\Zeus;

use GetOlympus\Zeus\Application\Controller\Application;

/**
 * OLYMPUS ZEUS CORE
 *
 * Library Name: Olympus Zeus Core
 * Version: 0.0.46
 * Snippet URI: https://github.com/GetOlympus/Zeus-Core
 * Read The Doc: http://olympus.readme.io/
 * Description: Olympus Zeus framework core system used to make all Olympus libraries
 *              work efficiently. Build with ♥ for WordPress developers.
 *
 * Author: Achraf Chouk
 * Author URI: https://github.com/crewstyle
 * License: The MIT License (MIT)
 *
 * The MIT License (MIT)
 *
 * Copyright (C) Achraf Chouk - achrafchouk@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

$file = dirname(__FILE__);
$path = dirname(dirname($file));

/**
 * Customizable constants.
 */

// Directory separator
defined('S')                    or define('S', DIRECTORY_SEPARATOR);
// Defining if we use Twig cache or not
defined('OL_ZEUS_USECACHE')     or define('OL_ZEUS_USECACHE', true);

/**
 * Package constants.
 */

// The path
define('OL_ZEUS_PATH', dirname(__FILE__));
// Capabilities
define('OL_ZEUS_WP_CAP', 'edit_posts');
// Path
$path = dirname(dirname(OL_ZEUS_PATH));

// Defining if we are in admin panel or not
define('OL_ZEUS_ISADMIN', defined('OL_ISADMIN') ? OL_ISADMIN : is_admin());
// Blog home url
define('OL_ZEUS_HOME', defined('OL_BLOG_HOME') ? OL_BLOG_HOME : get_option('home'));
// Blog name
define('OL_ZEUS_NAME', defined('OL_BLOG_NAME') ? OL_BLOG_NAME : get_bloginfo('name'));
// URI
define('OL_ZEUS_URI', defined('DISTPATH') ? str_replace(WEBPATH, '/../', DISTPATH) : OL_ZEUS_HOME.'/app/assets/');
// Language blog
define('OL_ZEUS_LOCAL', defined('OL_BLOG_LANGUAGE') ? OL_BLOG_LANGUAGE : get_bloginfo('language'));

// Twig cache folder
define('OL_ZEUS_CACHE', defined('CACHEPATH') ? CACHEPATH : $path.S.'app'.S.'cache'.S);
// Zeus Assets folder
define('OL_ZEUS_ASSETSPATH', $path.S.'app'.S.'assets'.S);
// Assets folder
define('OL_ZEUS_DISTPATH', defined('DISTPATH') ? DISTPATH : $path.S.'app'.S.'assets'.S);
// Languages folder
define('OL_ZEUS_LANGUAGES', $path.S.'languages');


/**
 * Master class.
 *
 * To get its own settings, define all functions used to build custom pages and
 * custom post types.
 *
 * @package OlympusZeusCore
 * @author  Achraf Chouk <achrafchouk@gmail.com>
 * @since   0.0.1
 *
 */

abstract class Zeus extends Application
{
    /**
     * @var array
     */
    protected $defaultfields = [
        // Zeus field components
        'GetOlympus\\Field\\Code',
        'GetOlympus\\Field\\Color',
        'GetOlympus\\Field\\Content',
        'GetOlympus\\Field\\Link',
        'GetOlympus\\Field\\Radio',
        'GetOlympus\\Field\\Select',
        'GetOlympus\\Field\\Text',
        'GetOlympus\\Field\\Textarea',
        'GetOlympus\\Field\\Toggle',
        'GetOlympus\\Field\\Upload',
        'GetOlympus\\Field\\Wordpress',
    ];
}
