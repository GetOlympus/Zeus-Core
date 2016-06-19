<?php

namespace GetOlympus\Hera;

use GetOlympus\Hera\Application\Controller\Application;

/**
 * OLYMPUS HERA
 *
 * Library Name: Olympus Hera
 * Version: 0.0.7
 * Snippet URI: https://github.com/GetOlympus/Hera
 * Read The Doc: http://olympus.readme.io/
 * Description: Core bundles of the Olympus framework, used by Olympus Zeus
 * and other bundles.
 *
 * Author: Achraf Chouk
 * Author URI: https://github.com/crewstyle
 * License: The MIT License (MIT)
 *
 * The MIT License (MIT)
 *
 * Copyright (C) 2016, Achraf Chouk - achrafchouk@gmail.com
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

// The path
define('OLH_PATH', dirname(__FILE__));

/**
 * Customizable constants.
 */

// Directory separator
defined('S')                or define('S', DIRECTORY_SEPARATOR);
// Defining if we are in admin panel or not
defined('OLH_ISADMIN')      or define('OLH_ISADMIN', is_admin());
// Nonce ajax value
defined('OLH_NONCE')        or define('OLH_NONCE', 'olympus-hera-ajax-nonce');
// Blog home url
defined('OLH_HOME')         or define('OLH_HOME', get_option('home'));
// Language blog
defined('OLH_LOCAL')        or define('OLH_LOCAL', get_bloginfo('language'));
// URI
defined('OLH_URI')          or define('OLH_URI', OLH_HOME.'/app/assets/');
// Twig cache folder
defined('OLH_CACHE')        or define('OLH_CACHE', dirname(dirname(OLH_PATH)).S.'app'.S.'cache'.S);

/**
 * Main constants.
 */

// Current version
define('OLH_VERSION', '0.0.7');
// Current baseline
define('OLH_QUOTE', 'I\'m a damsel, I\'m in distress, I can handle this. Have a nice day. ~ Hercules');
// Context used to define if the PHP files can be executed
define('OLH_CONTEXT', 'olympus-hera');
// Capabilities
define('OLH_WP_CAP', 'edit_posts');


/**
 * Master class.
 *
 * To get its own settings, define all functions used to build custom pages and
 * custom post types.
 *
 * @package Olympus Hera
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Hera extends Application
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Components to load
        $this->externals = array_merge($this->externals, [
            // Hera field components
            'Background'                => 'GetOlympus\Field\Background',
            'Checkbox'                  => 'GetOlympus\Field\Checkbox',
            'Code'                      => 'GetOlympus\Field\Code',
            'Color'                     => 'GetOlympus\Field\Color',
            'Date'                      => 'GetOlympus\Field\Date',
            'File'                      => 'GetOlympus\Field\File',
            'Font'                      => 'GetOlympus\Field\Font',
            'Hidden'                    => 'GetOlympus\Field\Hidden',
            'Html'                      => 'GetOlympus\Field\Html',
            'Link'                      => 'GetOlympus\Field\Link',
            'Map'                       => 'GetOlympus\Field\Map',
            'Multiselect'               => 'GetOlympus\Field\Multiselect',
            'Radio'                     => 'GetOlympus\Field\Radio',
            'Rte'                       => 'GetOlympus\Field\Rte',
            'Select'                    => 'GetOlympus\Field\Select',
            'Text'                      => 'GetOlympus\Field\Text',
            'Textarea'                  => 'GetOlympus\Field\Textarea',
            'Toggle'                    => 'GetOlympus\Field\Toggle',
            'Upload'                    => 'GetOlympus\Field\Upload',
            'Wordpress'                 => 'GetOlympus\Field\Wordpress',
        ]);

        // Use parent constructor
        parent::__construct();
    }

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

        // Iterate
        foreach ($this->externals as $alias => $component) {
            $class = new \ReflectionClass($component);
            $path = dirname(dirname($class->getFileName())).S.'Resources'.S;

            $externals[strtolower($alias)] = $path;
        }

        // Register all vendor views
        add_filter('olh_render_views', function ($paths) use ($externals){
            foreach ($externals as $alias => $path) {
                $paths[$alias] = $path.'views';
            }

            return $paths;
        });

        // Register all vendor translations
        add_filter('olh_translate_resources', function ($yamls) use ($externals){
            foreach ($externals as $alias => $path) {
                $yamls[$path.'languages'] = $alias.'field';
            }

            return $yamls;
        });
    }
}
