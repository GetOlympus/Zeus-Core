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
// Assets folder
define('OL_ZEUS_DISTPATH', defined('DISTPATH') ? DISTPATH : $path.S.'app'.S.'assets'.S);
// Twig cache folder
define('OL_ZEUS_CACHE', defined('CACHEPATH') ? CACHEPATH : $path.S.'app'.S.'cache'.S);


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
    protected $externals = [
        // Zeus field components
        'BackgroundField'   => 'GetOlympus\Field\Background',
        'CheckboxField'     => 'GetOlympus\Field\Checkbox',
        'CodeField'         => 'GetOlympus\Field\Code',
        'ColorField'        => 'GetOlympus\Field\Color',
        'DateField'         => 'GetOlympus\Field\Date',
        'FileField'         => 'GetOlympus\Field\File',
        'FontField'         => 'GetOlympus\Field\Font',
        'HiddenField'       => 'GetOlympus\Field\Hidden',
        'HtmlField'         => 'GetOlympus\Field\Html',
        'LinkField'         => 'GetOlympus\Field\Link',
        'MapField'          => 'GetOlympus\Field\Map',
        'MultiselectField'  => 'GetOlympus\Field\Multiselect',
        'RadioField'        => 'GetOlympus\Field\Radio',
        'RteField'          => 'GetOlympus\Field\Rte',
        'SelectField'       => 'GetOlympus\Field\Select',
        'SocialField'       => 'GetOlympus\Field\Social',
        'TextField'         => 'GetOlympus\Field\Text',
        'TextareaField'     => 'GetOlympus\Field\Textarea',
        'ToggleField'       => 'GetOlympus\Field\Toggle',
        'UploadField'       => 'GetOlympus\Field\Upload',
        'WordpressField'    => 'GetOlympus\Field\Wordpress',
    ];

    /**
     * @var array
     */
    protected $internals = [
        // Zeus common assets
        'js/dragndrop.js'   => 'Resources/assets/js/dragndrop/dragndrop.js',
        'js/modal.js'       => 'Resources/assets/js/modal/modal.js',
        'js/tooltip.js'     => 'Resources/assets/js/tooltip/tooltip.js',
    ];

    /**
     * Prepare externals.
     */
    protected function setExternals()
    {
        // Check externals
        if (empty($this->externals)) {
            return;
        }

        $externals = [];
        $internals = $this->internals;

        // Iterate
        foreach ($this->externals as $alias => $component) {
            $class = new \ReflectionClass($component);
            $path = dirname(dirname($class->getFileName())).S.'Resources'.S;

            $externals[strtolower($alias)] = $path;
        }

        // Register all vendor views
        add_filter('ol_zeus_render_views', function ($paths) use ($externals) {
            foreach ($externals as $alias => $path) {
                $alias = str_replace('field', '', $alias);
                $paths[$alias] = $path.'views';
            }

            return $paths;
        });

        // Register all internal assets
        add_filter('ol_zeus_render_assets', function ($paths) use ($internals) {
            foreach ($internals as $name => $path) {
                $paths[$name] = OL_ZEUS_PATH.S.$path;
            }

            return $paths;
        });

        // Register all vendor translations
        add_filter('ol_zeus_translate_resources', function ($yamls) use ($externals) {
            foreach ($externals as $alias => $path) {
                $yamls[$path.'languages'] = $alias;
            }

            return $yamls;
        });
    }
}
