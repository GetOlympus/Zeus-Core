# Olympus Zeus Core ![PHP Version][php-image]
> **Olympus Zeus Core** is a framework which allows you to make all your **WordPress** plugins and themes developments easier and efficient.

[![Olympus Component][olympus-image]][olympus-url]
[![CodeFactor Grade][codefactor-image]][codefactor-url]
[![Packagist Version][packagist-image]][packagist-url]
[![Travis Status][travis-image]][travis-url]

## Features

+ Better and secure folder structure
+ All **Olympus fields** integrated by default
+ Dependency management with [**Composer**](https://getcomposer.org)
+ PHPLeague [**dependency injection container**](https://github.com/thephpleague/container)
+ Symfony [**HTTP Foundation**](https://github.com/symfony/http-foundation) and [**Translation**](https://github.com/symfony/translation) components
+ [**Twig**](https://github.com/twigphp/Twig) renderer
+ And more...

![With Composer](https://img.shields.io/badge/with-Composer-885630.svg?style=flat-square)

## Installation

Using `composer` in your PHP project:

```sh
composer require getolympus/olympus-zeus-core
```

## Initialization

_In progress (soon, really soon)_

## Full example

Example from `functions.php` WordPress theme file:

```php
// file: functions.php
namespace MyThemeName;

/**
 * Everything starts here.
 *
 * @package MyThemeName
 * @author Your Name <yourmail@domain-name.ext>
 * @since x.y.z
 *
 */

// Directory separator and Vendor path.
defined('S')          or define('S', DIRECTORY_SEPARATOR);
defined('VENDORPATH') or define('VENDORPATH', realpath(dirname(__DIR__)).S.'vendor'.S);

/**
 * MyThemeName class definition
 */

if (!class_exists('MyThemeName')) {
    class MyThemeName extends \GetOlympus\Zeus\Zeus
    {
        protected $posttypes  = __DIR__.S.'controllers'.S.'posttypes';

        protected $configurations = [
            'Clean' => [
                'core'     => true,
                'features' => true,
                'headers'  => true,
                'plugins'  => true,
            ],
            'Sizes' => [
                'img-size-one' => [250, 250, true, __('Squared image', 'mythemename')],
                'img-size-two' => [1000, 90, true, __('Header image', 'mythemename')],
            ],
        ];

        /**
         * Constructor.
         */
        protected function setVars()
        {
            // Load Zeus framework vendors.
            if (file_exists($autoload = VENDORPATH.'autoload.php')) {
                include $autoload;
            }
        }
    }
}

// Instanciate MyThemeName
return new MyThemeName();
```

Example from `controllers/posttypes/MoviePosttype.php` controller file, assuming you need a new Movie custom post type:

```php
// file: controllers/posttypes/MoviePosttype.php
namespace MyThemeName\Controllers\Posttypes;

class MoviePosttype extends \GetOlympus\Zeus\Posttype\Controller\Posttype
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
        $this->getModel()->setLabels(array_merge(
            $this->getModel()->getLabels(),
            [
                'name' => __('Movies', 'mythemename'),
                'singular_name' => __('Movie', 'mythemename'),
            ]
        ));

        // Add metabox
        $this->addMetabox(__('Details', 'mythemename'), [
            \GetOlympus\Field\Text::build('link', [
                'title'     => __('Movie source URL', 'mythemename'),
            ]),
            \GetOlympus\Field\Text::build('length', [
                'title'     => __('Length in seconds', 'mythemename'),
            ]),
            \GetOlympus\Field\Text::build('author', [
                'title'     => __('Author name', 'mythemename'),
            ]),
            // (...)
        ]);
    }
}
```

## Release History

* 2.0.11 (December 11th, 2019)
- [x] ADD: new separates Helpers Plugins

* 2.0.10 (December 08th, 2019)
- [x] FIX: field access value from User controller

## Authors and Copyright

Achraf Chouk  
[![@crewstyle][twitter-image]][twitter-url]

Please, read [LICENSE][license-blob] for more information.  
[![MIT][license-image]][license-url]

[https://github.com/crewstyle](https://github.com/crewstyle)  
[http://fr.linkedin.com/in/achrafchouk](http://fr.linkedin.com/in/achrafchouk)

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
[codefactor-image]: https://www.codefactor.io/repository/github/GetOlympus/Zeus-Core/badge?style=flat-square
[codefactor-url]: https://www.codefactor.io/repository/github/getolympus/zeus-core
[license-blob]: https://github.com/GetOlympus/Zeus-Core/blob/master/LICENSE
[license-image]: https://img.shields.io/badge/license-MIT_License-blue.svg?style=flat-square
[license-url]: http://opensource.org/licenses/MIT
[packagist-image]: https://img.shields.io/packagist/v/getolympus/olympus-zeus-core.svg?style=flat-square
[packagist-url]: https://packagist.org/packages/getolympus/olympus-zeus-core
[php-image]: https://img.shields.io/travis/php-v/GetOlympus/Zeus-Core.svg?style=flat-square
[travis-image]: https://img.shields.io/travis/GetOlympus/Zeus-Core/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/GetOlympus/Zeus-Core
[twitter-image]: https://img.shields.io/badge/crewstyle-blue.svg?style=social&logo=twitter
[twitter-url]: http://twitter.com/crewstyle
