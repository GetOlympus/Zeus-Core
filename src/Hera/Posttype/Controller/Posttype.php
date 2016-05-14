<?php

namespace GetOlympus\Hera\Posttype\Controller;

use GetOlympus\Hera\Notification\Controller\Notification;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Posttype\Model\Posttype as PosttypeModel;
use GetOlympus\Hera\Posttype\Controller\PosttypeHook;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Gets its own post type.
 *
 * @package Olympus Hera
 * @subpackage Posttype\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Posttype
{
    /**
     * @var PosttypeModel
     */
    protected $posttype;

    /**
     * Constructor.
     */
    public function __construct(){}

    /**
     * Initialization.
     *
     * @param string $slug
     * @param array $args
     * @param array $labels
     */
    public function initialize($slug, $args, $labels)
    {
        if (empty($labels) || !isset($labels['plural'], $labels['singular']) || empty($labels['plural']) || empty($labels['singular'])) {
            Notification::error(Translate::t('posttype.errors.term_is_not_defined'));

            return;
        }

        $this->posttype = new PosttypeModel();

        $slug = Render::urlize($slug);
        $args = array_merge($this->defaultArgs($slug), $args);
        $args['labels'] = array_merge($this->defaultLabels($labels['plural'], $labels['singular']), $labels);

        // Update vars
        $this->posttype->setSlug($slug);
        $this->posttype->setArgs($args);
    }

    /**
     * Build args.
     *
     * @param string $slug
     * @return array $args
     */
    protected function defaultArgs($slug)
    {
        return [
            'can_export' => true,
            'capability_type' => 'post',
            'description' => '',
            'exclude_from_search' => false,
            'has_archive' => false,
            'hierarchical' => false,
            'menu_icon' => '',
            'menu_position' => 100,
            'public' => false,
            'publicly_queryable' => false,

            'permalink_epmask' => EP_PERMALINK,
            'query_var' => true,
            'rewrite' => false,
            'show_in_menu' => true,
            'show_ui' => true,

            'supports' => [],
            'taxonomies' => [],

            'show_in_rest' => true,
            'rest_base' => $slug,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];
    }

    /**
     * Build labels.
     *
     * @param string $plural
     * @param string $singular
     * @return array $labels
     */
    protected function defaultLabels($plural, $singular)
    {
        return [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => $plural,

            'add_new' => Translate::t('posttype.defaults.labels.add_new'),
            'add_new_item' => Translate::t('posttype.defaults.labels.add_new_item'),
            'edit' => Translate::t('posttype.defaults.labels.edit'),
            'edit_item' => Translate::t('posttype.defaults.labels.edit_item'),
            'new_item' => Translate::t('posttype.defaults.labels.new_item'),

            'not_found' => Translate::t('posttype.defaults.labels.not_found'),
            'not_found_in_trash' => Translate::t('posttype.defaults.labels.not_found_in_trash'),
            'parent_item_colon' => Translate::t('posttype.defaults.labels.parent_item_colon'),
            'search_items' => Translate::t('posttype.defaults.labels.search_items'),

            'view' => Translate::t('posttype.defaults.labels.view'),
            'view_item' => Translate::t('posttype.defaults.labels.view_item'),
        ];
    }

    /**
     * Register post types.
     */
    protected function register()
    {
        add_action('init', function (){
            // Store details
            $slug = $this->posttype->getSlug();
            $args = $this->posttype->getArgs();

            // Special case: define a post/page as title
            // to edit default post/page component
            if (in_array($slug, ['post', 'page'])) {
                return [];
            }

            // Check if post type already exists
            if (post_type_exists($slug)) {
                return [];
            }

            // Action to register
            register_post_type($slug, $args);

            // Update post type
            $posttype = array_merge($posttype, $args);

            // Option
            $opt = str_replace('%SLUG%', $slug, '%SLUG%-olz-structure');

            // Get value
            $structure = Option::get($opt, '/%'.$slug.'%-%post_id%');

            // Change structure
            add_rewrite_tag('%'.$slug.'%', '([^/]+)', $slug.'=');
            add_permastruct($slug, $structure, false);

            // Works on hook
            $hook = new PosttypeHook($slug);
            $this->posttype->setHook($hook);
        }, 10, 1);
    }
}
