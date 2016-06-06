<?php

namespace GetOlympus\Hera;

use GetOlympus\Hera\Application\Controller\Application;

/**
 * OLYMPUS HERA
 *
 * Library Name: Olympus Hera
 * Version: 0.0.1
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
// Directory
defined('S') or define('S', DIRECTORY_SEPARATOR);

/**
 * Customizable constants.
 */

// The value defining if we are in admin panel or not
defined('OLH_ISADMIN')      or define('OLH_ISADMIN', is_admin());
// The nonce ajax value
defined('OLH_NONCE')        or define('OLH_NONCE', 'olympus-hera-ajax-nonce');
// The blog home url
defined('OLH_HOME')         or define('OLH_HOME', get_option('home'));
// The language blog
defined('OLH_LOCAL')        or define('OLH_LOCAL', get_bloginfo('language'));
// The URI
defined('OLH_URI')          or define('OLH_URI', OLH_HOME.'/app/assets/');
// The Twig cache folder
defined('OLH_CACHE')        or define('OLH_CACHE', dirname(dirname(OLH_PATH)).S.'app'.S.'cache'.S);

/**
 * Main constants.
 */

// The context used to define if the PHP files can be executed
define('OLH_CONTEXT', 'olympus-hera');
// The current version
define('OLH_VERSION', '0.0.1');
// The current version
define('OLH_VERSION_NUM', str_replace('.', '', OLH_VERSION));
// The current baseline
define('OLH_QUOTE', 'I\'m a damsel, I\'m in distress, I can handle this. Have a nice day. ~ Hercules');
// The value defining if theme uses post thumbnails or not
define('OLH_CAN_THUMB', current_theme_supports('post-thumbnails'));
// The value defining if theme uses post thumbnails or not
define('OLH_CAN_UPLOAD', current_user_can('upload_files'));
// The capabilities
define('OLH_WP_CAP', 'edit_posts');
// The custom capabilities
define('OLH_WP_CAP_MAX', 'manage_tea_theme_options');


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

class Hera extends Application
{
    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        /**
         * Update identifier.
         *
         * @param string $identifier
         * @return string $identifier
         *
         * @since 0.0.1
         */
        $this->identifier = apply_filters('olh_hera_identifier', 'olympus');

        // Components to load
        $this->components = array_merge($this->components, [
            // Hera field components
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
            'SectionField'      => 'GetOlympus\Field\Section',
            'SelectField'       => 'GetOlympus\Field\Select',
            'TextField'         => 'GetOlympus\Field\Text',
            'TextareaField'     => 'GetOlympus\Field\Textarea',
            'ToggleField'       => 'GetOlympus\Field\Toggle',
            'UploadField'       => 'GetOlympus\Field\Upload',
            'WordpressField'    => 'GetOlympus\Field\Wordpress',
        ]);

        // Use parent constructor
        parent::__construct();
    }
}
