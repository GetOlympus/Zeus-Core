<?php

namespace GetOlympus\Hera\Posttype\Controller;

use GetOlympus\Hera\Notification\Controller\Notification;
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

abstract class Posttype implements PosttypeInterface
{
    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    protected $args;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $forbidden_slugs = ['action', 'author', 'order', 'theme'];

    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#labels
     */
    protected $labels;

    /**
     * @var PosttypeModel
     */
    protected $posttype;

    /**
     * @var array
     */
    protected $reserved_slugs = ['post', 'page', 'attachment', 'revision', 'nav_menu_item'];

    /**
     * @var string
     */
    protected $slug;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Update slug
        $this->slug = Render::urlize($this->slug);

        // Check forbidden slugs
        if (in_array($this->slug, $this->forbidden_slugs)) {
            throw new PosttypeException(Translate::t('posttype.errors.slug_is_forbidden'));
        }

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Build PosttypeModel and initialize hook.
     */
    public function init()
    {
        // Initialize PosttypeModel
        $this->posttype = new PosttypeModel();
        $this->posttype->setFields($this->fields);
        $this->posttype->setSlug($this->slug);

        // Update args on post types except reserved ones
        if (!in_array($this->slug, $this->reserved_slugs)) {
            // Check if post type already exists
            if (post_type_exists($this->slug)) {
                throw new PosttypeException(Translate::t('posttype.errors.slug_already_exists'));
            }

            // Initialize plural and singular vars
            $this->labels['name'] = isset($this->labels['name']) ? $this->labels['name'] : '';
            $this->labels['singular_name'] = isset($this->labels['singular_name']) ? $this->labels['singular_name'] : '';

            // Check label for all except reserved ones
            if (empty($this->labels['name']) || empty($this->labels['singular_name'])) {
                throw new PosttypeException(Translate::t('posttype.errors.term_is_not_defined'));
            }

            $this->args = array_merge($this->defaultArgs(), $this->args);
            $this->args['labels'] = array_merge(
                $this->defaultLabels($this->labels['name'], $this->labels['singular_name']),
                $this->labels
            );

            // Update PosttypeModel args
            $this->posttype->setArgs($this->args);
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
        $slug = $this->posttype->getSlug();

        // Return args
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
        $slug = $this->posttype->getSlug();
        $args = $this->posttype->getArgs();
        $fields = $this->posttype->getFields();

        // Register post type if not post or page
        if (!in_array($slug, $this->reserved_slugs)) {
            // Action to register
            register_post_type($slug, $args);

            // Option
            $opt = str_replace('%SLUG%', $slug, '%SLUG%-olympus-structure');

            // Get value
            $structure = Option::get($opt, '/%'.$slug.'%-%post_id%');

            // Change structure
            add_rewrite_tag('%'.$slug.'%', '([^/]+)', $slug.'=');
            add_permastruct($slug, $structure, false);
        }

        // Check name
        $name = isset($args['labels']['name']) ? $args['labels']['name'] : '';

        // Works on hook
        $hook = new PosttypeHook($slug, $name, $fields, $this->reserved_slugs);
        $this->posttype->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
