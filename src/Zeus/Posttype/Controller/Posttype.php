<?php

namespace GetOlympus\Zeus\Posttype\Controller;

use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Posttype\Controller\PosttypeHook;
use GetOlympus\Zeus\Posttype\Exception\PosttypeException;
use GetOlympus\Zeus\Posttype\Interface\PosttypeInterface;
use GetOlympus\Zeus\Posttype\Model\PosttypeModel;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Gets its own post type.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Posttype extends Base implements PosttypeInterface
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $forbidden_slugs = ['action', 'author', 'order', 'theme'];

    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var array
     */
    protected $reserved_slugs = ['post', 'page', 'attachment', 'revision', 'nav_menu_item'];

    /**
     * @var string
     */
    protected $singular_name = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize PosttypeModel
        $this->model = new PosttypeModel();

        // Update model
        $this->getModel()->setSlug(Helpers::urlize($this->slug));
        $this->getModel()->setArgs(array_merge([
            'menu_icon'  => 'dashicons-dashboard',
            'supports'   => ['title'],
            'taxonomies' => [],
        ], $this->args));
        $this->getModel()->setLabels(array_merge([
            'name'          => $this->name,
            'singular_name' => $this->singular_name,
        ], $this->labels));

        // Add metaboxes and more
        $this->setVars();
        $this->init();
    }

    /**
     * Adds a new metabox.
     *
     * @param  string  $title
     * @param  array   $fields
     */
    public function addMetabox($title, $fields)
    {
        // Check title
        if (empty($title)) {
            throw new PosttypeException(Translate::t('posttype.errors.metabox_title_is_not_defined'));
        }

        // Check fields
        if (empty($fields)) {
            throw new PosttypeException(Translate::t('posttype.errors.metabox_fields_are_not_defined'));
        }

        $identifier = Helpers::urlize($title);

        // Update metaboxes
        $this->getModel()->setMetabox($identifier, $title, $fields);
    }

    /**
     * Return post types reserved slugs.
     *
     * @param  array   $reserved_slugs
     */
    public function getReservedSlugs()
    {
        return $this->reserved_slugs;
    }

    /**
     * Build PosttypeModel and initialize hook.
     */
    public function init()
    {
        $slug = $this->getModel()->getSlug();

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

            $args   = $this->getModel()->getArgs();
            $labels = $this->getModel()->getLabels();

            // Initialize plural and singular vars
            $labels['name']          = isset($labels['name']) ? $labels['name'] : '';
            $labels['singular_name'] = isset($labels['singular_name']) ? $labels['singular_name'] : '';

            // Check label for all except reserved ones
            if (empty($labels['name']) || empty($labels['singular_name'])) {
                throw new PosttypeException(Translate::t('posttype.errors.missing_singular_or_plural'));
            }

            $args   = array_merge($this->defaultArgs(), $args);
            $labels = array_merge($this->defaultLabels($labels['name'], $labels['singular_name']), $labels);

            $args['labels'] = $labels;

            // Update args and labels
            $this->getModel()->setArgs($args);
            $this->getModel()->setLabels($labels);
        }

        // Register post type
        $this->register();
    }

    /**
     * Build args.
     *
     * @return array   $args
     */
    public function defaultArgs()
    {
        return [
            'can_export'            => true,
            'capability_type'       => 'post',
            'description'           => '',
            'exclude_from_search'   => false,
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_icon'             => '',
            'menu_position'         => 100,
            'public'                => true,
            'publicly_queryable'    => true,

            'permalink_epmask'      => EP_PERMALINK,
            'query_var'             => true,
            'rewrite'               => false,
            'show_in_menu'          => true,
            'show_ui'               => true,

            'supports'              => [],
            'taxonomies'            => [],

            'show_in_rest'          => true,
            'rest_base'             => $this->getModel()->getSlug(),
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];
    }

    /**
     * Build labels.
     *
     * @param  string  $plural
     * @param  string  $singular
     * @return array   $labels
     */
    public function defaultLabels($plural, $singular)
    {
        return [
            'name'               => $plural,
            'singular_name'      => $singular,
            'menu_name'          => $plural,
            'all_items'          => $plural,

            'add_new'            => Translate::t('posttype.labels.add_new'),
            'add_new_item'       => Translate::t('posttype.labels.add_new_item'),
            'edit'               => Translate::t('posttype.labels.edit'),
            'edit_item'          => Translate::t('posttype.labels.edit_item'),
            'new_item'           => Translate::t('posttype.labels.new_item'),

            'not_found'          => Translate::t('posttype.labels.not_found'),
            'not_found_in_trash' => Translate::t('posttype.labels.not_found_in_trash'),
            'parent_item_colon'  => Translate::t('posttype.labels.parent_item_colon'),
            'search_items'       => Translate::t('posttype.labels.search_items'),

            'view'               => Translate::t('posttype.labels.view'),
            'view_item'          => Translate::t('posttype.labels.view_item'),
        ];
    }

    /**
     * Build statuses.
     *
     * @param  string  $name
     * @return array   $statuses
     */
    public function defaultStatuses($name)
    {
        $noop = $name.' <span class="count">(%s)</span>';

        return [
            'label'                     => $name,
            'public'                    => false,
            'internal'                  => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => false,
            'label_count'               => Translate::noop($noop, $noop),
        ];
    }

    /**
     * Register post types.
     */
    public function register()
    {
        $args    = $this->getModel()->getArgs();
        $slug    = $this->getModel()->getSlug();

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

        // Works on hook
        new PosttypeHook($this);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
