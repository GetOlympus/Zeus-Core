<?php

namespace GetOlympus\Hera\Render\Controller;

use GetOlympus\Hera\AdminPage\Controller\AdminPageHook;
use GetOlympus\Hera\Render\Controller\RenderInterface;
use GetOlympus\Hera\Request\Controller\Request;
use Behat\Transliterator\Transliterator;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * Render HTML entities.
 *
 * @package Olympus Hera
 * @subpackage Render\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Render implements RenderInterface
{
    /**
     * @var Singleton
     */
    private static $instance;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param array $components
     */
    public function __construct($components = [])
    {
        // Build all views folders to add
        $paths = [
            'adminpage'     => OLH_PATH.S.'AdminPage'.S.'Resources'.S.'views',
            'core'          => OLH_PATH.S.'Resources'.S.'views',
            'field'         => OLH_PATH.S.'Field'.S.'Resources'.S.'views',
            'metabox'       => OLH_PATH.S.'Metabox'.S.'Resources'.S.'views',
            'posttype'      => OLH_PATH.S.'Posttype'.S.'Resources'.S.'views',
            'user'          => OLH_PATH.S.'User'.S.'Resources'.S.'views',
            'widget'        => OLH_PATH.S.'Widget'.S.'Resources'.S.'views',
        ];

        /**
         * Add your custom views folder with alias.
         *
         * @param   array $paths
         * @return  array $paths
         */
        $paths = apply_filters('olh_render_views', $paths);

        // Define Twig loaders
        $loader = new Twig_Loader_Filesystem();

        // Add Hera core paths with alias
        foreach ($paths as $alias => $path) {
            $loader->addPath($path, $alias);
        }

        // Build Twig renderer
        $this->twig = new Twig_Environment($loader/*, ['cache' => OLH_CACHE]*/);


        /**
         * WORDPRESS functions
         */

        // Author
        $this->twig->addFunction(new Twig_SimpleFunction('get_the_author_meta', function ($display, $id) {
            get_the_author_meta($display, $id);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('get_author_posts_url', function ($id) {
            get_author_posts_url($id);
        }));

        // Image
        $this->twig->addFunction(new Twig_SimpleFunction('has_post_thumbnail', function ($id) {
            has_post_thumbnail($id);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('get_post_thumbnail_id', function ($id) {
            get_post_thumbnail_id($id);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('wp_get_attachment_image_src', function ($id, $format) {
            wp_get_attachment_image_src($id, $format);
        }));

        // Permalink
        $this->twig->addFunction(new Twig_SimpleFunction('get_permalink', function ($id) {
            get_permalink($id);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('get_term_link', function ($id, $type) {
            get_term_link($id, $type);
        }));

        // Template
        $this->twig->addFunction(new Twig_SimpleFunction('get_footer', function ($file = '') {
            get_footer($file);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('get_header', function ($file = '') {
            get_header($file);
        }));

        // Terms
        $this->twig->addFunction(new Twig_SimpleFunction('get_the_term_list', function ($id, $type, $before, $inside, $after) {
            get_the_term_list($id, $type, $before, $inside, $after);
        }));

        // wpEditor
        $this->twig->addFunction(new Twig_SimpleFunction('wp_editor', function ($content, $editor_id, $settings = []) {
            wp_editor($content, $editor_id, $settings);
        }));


        /**
         * OLYMPUS functions
         */

        // Dump array
        $this->twig->addFunction(new Twig_SimpleFunction('dump', function ($array) {
            echo '<pre>'; var_dump($array); echo '</pre>';
        }));

        // File inclusion
        $this->twig->addFunction(new Twig_SimpleFunction('include_file', function ($file) {
            include($file);
        }));


        /**
         * YOUR OWN functions
         */

        /**
         * Add your custom Twig functions.
         *
         * @param Twig_Environment $twig
         */
        do_action('olh_render_functions', $this->twig);
    }

    /**
     * Get singleton.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Render assets on asked page.
     *
     * @param array $currentPage
     * @param array $fields
     */
    public static function assets($currentPage, $fields)
    {
        global $pagenow;

        // Check current page
        if (empty($fields) || !in_array($pagenow, $currentPage)) {
            return;
        }

        $assets = [
            'scripts' => [],
            'styles' => [],
        ];

        /**
         * Add your custom assets to make them accessible.
         *
         * @param   array $paths
         * @return  array $paths
         */
        $paths = apply_filters('olh_render_assets', []);

        // Iterate on fields to render assets
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (!$field) {
                    continue;
                }

                $paths = array_merge($paths, $field->renderAssets());
            }
        }

        // Check paths
        if (empty($paths)) {
            return;
        }

        // Create temp accessible files and update assets
        foreach ($paths as $name => $path) {
            self::assetsInCache($path, $name);
            $type = 'js/' === substr($name, 0, 3) ? 'scripts' : 'styles';

            $assets[$type][] = [
                'name' => $name,
                'file' => OLH_URI.'dist/'.$name,
            ];
        }

        // Add all in admin panel
        add_action('admin_enqueue_scripts', function ($hook) use ($assets) {
            // Scripts
            if (!empty($assets['scripts'])) {
                foreach ($assets['scripts'] as $script) {
                    wp_enqueue_script($script['name'], $script['file'], ['jquery']);
                }
            }

            // Styles
            if (!empty($assets['styles'])) {
                $handle = wp_style_is('olympus-core', 'registered') ? 'olympus-core' : false;

                foreach ($assets['styles'] as $style) {
                    wp_enqueue_style($style['name'], $style['file'], $handle);
                }
            }
        });
    }

    /**
     * Create temporary asset accessible file.
     *
     * @param string $source
     * @param string $filename
     */
    public static function assetsInCache($source, $filename)
    {
        $dest = OLH_ASSETS.'dist'.S.$filename;

        // Create file
        if (!file_exists($dest)) {
            file_put_contents($dest, "/**\n * This file is auto-generated\n */\n\n".file_get_contents($source)."\n");
        }
    }

    /**
     * Camelize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $camelized
     */
    public static function camelCase($text, $separator = '-')
    {
        $slugified = self::urlize($text, $separator);
        $camel = strtolower($slugified);
        $camel = ucwords($camel, $separator);

        return str_replace($separator, '', $camel);
    }

    /**
     * Functionize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $functionized
     */
    public static function toFunction($text, $separator = '-')
    {
        $camelized = self::camelCase($text, $separator);

        return lcfirst($camelized);
    }

    /**
     * Slugify string.
     *
     * @param string $text
     * @param string $separator
     * @return string $slugified
     */
    public static function urlize($text, $separator = '-')
    {
        return Transliterator::urlize($text, $separator);
    }

    /**
     * Render TWIG component.
     *
     * @param string $template
     * @param array $vars
     * @param string $context
     */
    public static function view($template, $vars, $context = 'core')
    {
        // Display template
        echo self::getInstance()->twig->render('@'.$context.'/'.$template, $vars);
    }
}
