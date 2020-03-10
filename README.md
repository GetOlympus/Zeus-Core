# Olympus Zeus Core ![PHP Version][php-image]
> **Olympus Zeus Core** is a framework which allows you to make all your **WordPress** plugins and themes developments easier and efficient.

```sh
composer require getolympus/olympus-zeus-core
```

---

[![Olympus Component][olympus-image]][olympus-url]
[![CodeFactor Grade][codefactor-image]][codefactor-url]
[![Packagist Version][packagist-image]][packagist-url]
[![Travis Status][travis-image]][travis-url]
[![MIT][license-image]][license-blob]

---

## Features

+ Better and secure folder structure
+ All **Olympus fields** integrated by default
+ Olympus [**Hera Renderer**](https://github.com/GetOlympus/Hera-Renderer) and [**Hermes Translator**](https://github.com/GetOlympus/Hermes-Translator)
+ Symfony [**HTTP Foundation**](https://github.com/symfony/http-foundation) and [**Class loader**](https://github.com/symfony/class-loader) components
+ Dependency management with [**Composer**](https://getcomposer.org)
+ And more...

![With Composer](https://img.shields.io/badge/with-Composer-885630.svg?style=flat-square)

## Initialization

To initialize `Zeus Core` from your `functions.php` WordPress theme file or main plugin php file:

```php
// file: functions.php
namespace MyThemeName;

/**
 * Everything starts here.
 *
 * @package MyThemeName
 * @author  Your Name <yourmail@domain-name.ext>
 * @since   x.y.z
 *
 */

// Directory separator and Vendor path.
defined('S')          or define('S', DIRECTORY_SEPARATOR); // Provided by Olympus container
defined('VENDORPATH') or define('VENDORPATH', realpath(dirname(__DIR__)).S.'vendor'.S); // Provided by Olympus container

/**
 * MyThemeName class definition
 */

if (!class_exists('MyThemeName')) {
    /**
     * Use of Zeus abstract PHP class to initialize everything.
     */
    class MyThemeName extends \GetOlympus\Zeus\Zeus
    {
        /**
         * Define all useful folders
         */
        // Load option admin pages
        protected $adminpages = __DIR__.S.'controllers'.S.'adminpages';
        // Load scheduled actions
        protected $crons      = __DIR__.S.'controllers'.S.'crons';
        // Load custom post types
        protected $posttypes  = __DIR__.S.'controllers'.S.'posttypes';
        // Load custom terms
        protected $terms      = __DIR__.S.'controllers'.S.'terms';
        // Load options for users
        protected $users      = __DIR__.S.'controllers'.S.'users';
        // Load custom widgets
        protected $widgets    = __DIR__.S.'controllers'.S.'widgets';

        /**
         * Define WordPress optimizations and configurations in a single var.
         */
        protected $configurations = [
            'AccessManagement' => [/*...*/],
            'Assets'           => [/*...*/],
            'Clean'            => [/*...*/],
            'Menus'            => [/*...*/],
            'Settings'         => [/*...*/],
            'Shortcodes'       => [/*...*/],
            'Sidebars'         => [/*...*/],
            'Sizes'            => [/*...*/],
            'Supports'         => [/*...*/],
        ];

        /**
         * Main function which defines vendors path
         * and some useful actions needed by your application
         */
        protected function setVars()
        {
            // Load Zeus framework vendors.
            if (file_exists($autoload = VENDORPATH.'autoload.php')) {
                include $autoload;
            }

            // Add custom actions.
        }
    }
}

// Instanciate MyThemeName
return new MyThemeName();
```

## A custom post type example

Assuming you need a new `Movie` custom post type, here is the `controllers/posttypes/MoviePosttype.php` content file:

```php
// file: controllers/posttypes/MoviePosttype.php
namespace MyThemeName\Controllers\Posttypes;

/**
 * Extends main \GetOlympus\Zeus\Posttype\Posttype class to use all functionalities
 */
class MoviePosttype extends \GetOlympus\Zeus\Posttype\Posttype
{
    /**
     * @var array
     */
    protected $args = [
        'menu_icon'     => 'dashicons-video-alt3',
        'supports'      => ['title', 'excerpt', 'thumbnail'],
        'taxonomies'    => ['post_tag'],
        'rewrite'       => [
            'slug'          => 'movie',
            'with_front'    => true,
        ],
    ];

    /**
     * @var string
     */
    protected $slug = 'movie';

    /**
     * Prepare variables.
     */
    public function setVars()
    {
        // Update labels
        $this->setLabels([
            'name'          => __('Movies', 'mythemename'),
            'singular_name' => __('Movie', 'mythemename'),
        ]);

        // Add metabox
        $this->addMetabox(__('Details', 'mythemename'), [
            \GetOlympus\Dionysos\Field\Text::build('link', [
                'title' => __('Movie source URL', 'mythemename'),
            ]),
            \GetOlympus\Dionysos\Field\Text::build('length', [
                'title' => __('Length in seconds', 'mythemename'),
            ]),
            \GetOlympus\Dionysos\Field\Text::build('author', [
                'title' => __('Author name', 'mythemename'),
            ]),
            // (...)
        ]);
    }
}
```

## Release History

See [**CHANGELOG.md**][changelog-blob] for all details.

## Contributing

1. Fork it (<https://github.com/GetOlympus/Zeus-Core/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

---

**Built with ♥ by [Achraf Chouk](http://github.com/crewstyle "Achraf Chouk") ~ (c) since a long time.**

<!-- links & imgs dfn's -->
[olympus-image]: https://img.shields.io/badge/for-Olympus-44cc11.svg?style=flat-square
[olympus-url]: https://github.com/GetOlympus
[changelog-blob]: https://github.com/GetOlympus/Zeus-Core/blob/master/CHANGELOG.md
[codefactor-image]: https://www.codefactor.io/repository/github/GetOlympus/Zeus-Core/badge?style=flat-square
[codefactor-url]: https://www.codefactor.io/repository/github/getolympus/zeus-core
[license-blob]: https://github.com/GetOlympus/Zeus-Core/blob/master/LICENSE
[license-image]: https://img.shields.io/badge/license-MIT_License-blue.svg?style=flat-square
[packagist-image]: https://img.shields.io/packagist/v/getolympus/olympus-zeus-core.svg?style=flat-square
[packagist-url]: https://packagist.org/packages/getolympus/olympus-zeus-core
[php-image]: https://img.shields.io/travis/php-v/GetOlympus/Zeus-Core.svg?style=flat-square
[travis-image]: https://img.shields.io/travis/GetOlympus/Zeus-Core/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/GetOlympus/Zeus-Core