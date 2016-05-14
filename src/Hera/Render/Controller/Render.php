<?php

namespace GetOlympus\Hera\Render\Controller;

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

class Render
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
            'field'         => OLH_PATH.S.'Field'.S.'Resources'.S.'views',
            'notification'  => OLH_PATH.S.'Notification'.S.'Resources'.S.'views',
            'posttype'      => OLH_PATH.S.'Posttype'.S.'Resources'.S.'views',
            'template'      => OLH_PATH.S.'Template'.S.'Resources'.S.'views',
        ];

        // Define Twig loaders
        $loader = new Twig_Loader_Filesystem();

        // Add Hera core paths with alias
        foreach ($paths as $alias => $path) {
            $loader->addPath($path, $alias);
        }

        // Check components
        $components = [
            // Hera field components
            'background'    => 'GetOlympus\Field\Background',
            'checkbox'      => 'GetOlympus\Field\Checkbox',
            'code'          => 'GetOlympus\Field\Code',
            'color'         => 'GetOlympus\Field\Color',
            'date'          => 'GetOlympus\Field\Date',
            'file'          => 'GetOlympus\Field\File',
            'font'          => 'GetOlympus\Field\Font',
            'hidden'        => 'GetOlympus\Field\Hidden',
            'html'          => 'GetOlympus\Field\Html',
            'link'          => 'GetOlympus\Field\Link',
            'map'           => 'GetOlympus\Field\Map',
            'multiselect'   => 'GetOlympus\Field\Multiselect',
            'radio'         => 'GetOlympus\Field\Radio',
            'rte'           => 'GetOlympus\Field\Rte',
            'section'       => 'GetOlympus\Field\Section',
            'select'        => 'GetOlympus\Field\Select',
            'text'          => 'GetOlympus\Field\Text',
            'textarea'      => 'GetOlympus\Field\Textarea',
            'toggle'        => 'GetOlympus\Field\Toggle',
            'upload'        => 'GetOlympus\Field\Upload',
            'wordpress'     => 'GetOlympus\Field\Wordpress',
        ];

        // Vendors path
        $vendor = defined('VENDORPATH') ? VENDORPATH : OLH_PATH.S.'..'.S.'..'.S.'vendor'.S;

        // Register all render views
        foreach ($components as $alias => $donotget) {
            $path = $vendor.'getolympus'.S.'olympus-'.$alias.'-field'.S.'src'.S.'Resources'.S.'views';

            $loader->addPath($path, $alias.'Field');
        }

        /**
         * Add your custom views folder with alias.
         *
         * @param Twig_Loader_Filesystem $loader
         */
        do_action('olh_render_views', $loader);

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
    public static function view($template, $vars, $context = 'template')
    {
        // Display template
        echo self::getInstance()->twig->render('@'.$context.'/'.$template, $vars);
    }
}
