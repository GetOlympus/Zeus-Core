<?php

namespace GetOlympus\Hera\Posttype\Controller;

use GetOlympus\Hera\Base\Controller\Base;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Posttype\Controller\PosttypeHook;
use GetOlympus\Hera\Posttype\Controller\PosttypeInterface;
use GetOlympus\Hera\Posttype\Exception\PosttypeException;
use GetOlympus\Hera\Posttype\Model\PosttypeModel;
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

abstract class Posttype extends Base implements PosttypeInterface
{
    /**
     * @var array
     */
    protected $forbidden_slugs = ['action', 'author', 'order', 'theme'];

    /**
     * @var array
     */
    protected $reserved_slugs = ['post', 'page', 'attachment', 'revision', 'nav_menu_item'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize PosttypeModel
        $this->model = new PosttypeModel();

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Build PosttypeModel and initialize hook.
     */
    public function init()
    {
        // Update slug
        $slug = Render::urlize($this->getModel()->getSlug());
        $this->getModel()->setSlug($slug);

        // Check forbidden slugs
        if (in_array($slug, $this->forbidden_slugs)) {
            throw new PosttypeException(Translate::t('posttype.errors.slug_is_forbidden'));
        }

        // Update args on post types except reserved ones
        if (!in_array($slug, $this->reserved_slugs)) {
            // Check if post type already exists
            if (post_type_exists($slug)) {
                throw new PosttypeException(Translate::t('posttype.errors.slug_already_exists'));
            }

            $args = $this->getModel()->getArgs();
            $labels = $this->getModel()->getLabels();

            // Initialize plural and singular vars
            $labels['name'] = isset($labels['name']) ? $labels['name'] : '';
            $labels['singular_name'] = isset($labels['singular_name']) ? $labels['singular_name'] : '';

            // Check label for all except reserved ones
            if (empty($labels['name']) || empty($labels['singular_name'])) {
                throw new PosttypeException(Translate::t('posttype.errors.term_is_not_defined'));
            }

            $args = array_merge($this->defaultArgs(), $args);
            $args['rewrite'] = false;
            $args['labels'] = array_merge(
                $this->defaultLabels($labels['name'], $labels['singular_name']),
                $labels
            );

            // Update PosttypeModel args
            $this->getModel()->setArgs($args);
            $this->getModel()->setLabels($labels);
        }

        // Register post type
        $this->register();
    }

    /**
     * Build args.
     *
     * @return array $args
     */
    public function defaultArgs()
    {
        // Get slug
        $slug = $this->getModel()->getSlug();

        // Return args
        return [
            'can_export' => true,
            'capability_type' => 'post',
            'description' => '',
            'exclude_from_search' => false,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => '',
            'menu_position' => 100,
            'public' => true,
            'publicly_queryable' => true,

            'permalink_epmask' => EP_PERMALINK,
            'query_var' => true,
            'rewrite' => [
                'slug' => $slug,
                'with_front' => true,
                'feeds' => true,
                'pages' => true,
            ],
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
    public function defaultLabels($plural, $singular)
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
     * Build statuses.
     *
     * @param string $name
     * @return array $statuses
     */
    public function defaultStatuses($name)
    {
        return [
            'label' => $name,
            'public' => false,
            'internal' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => false,
            'label_count' => Translate::n($name.' <span class="count">(%s)</span>', $name.' <span class="count">(%s)</span>'),
        ];
    }

    /**
     * Register post types.
     */
    public function register()
    {
        // Store details
        $args = $this->getModel()->getArgs();
        $metaboxes = $this->getModel()->getMetaboxes();
        $slug = $this->getModel()->getSlug();

        // Register post type if not post or page
        if (!in_array($slug, $this->reserved_slugs)) {
            global $wp_rewrite;

            // Action to register
            register_post_type($slug, $args);

            // Option
            $opt = 'permalink_structure_'.$slug;

            // Get value
            $structure = Option::get($opt, '/%'.$slug.'%-%post_id%');

            // Change structure
            $wp_rewrite->add_rewrite_tag('%'.$slug.'%', '([^/]+)', $slug.'=');
            $wp_rewrite->add_permastruct($slug, $structure, false);
        }

        // Check name
        $name = isset($args['labels']['name']) ? $args['labels']['name'] : '';

        // Works on hook
        $hook = new PosttypeHook($slug, $name, $metaboxes, $this->reserved_slugs);
        $this->getModel()->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
