<?php

namespace GetOlympus\Zeus\Posttype;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Posttype\PosttypeHook;
use GetOlympus\Zeus\Posttype\PosttypeException;
use GetOlympus\Zeus\Posttype\PosttypeInterface;
use GetOlympus\Zeus\Posttype\PosttypeModel;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own post type.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype
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
        $this->setSlug($this->slug);
        $this->setArgs(array_merge([
            'menu_icon'  => 'dashicons-dashboard',
            'supports'   => ['title'],
            'taxonomies' => [],
        ], $this->args));
        $this->setLabels(array_merge([
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
     *
     * @throws PosttypeException
     */
    public function addMetabox($title, $fields) : void
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
     * @return array
     */
    public function getReservedSlugs() : array
    {
        return $this->reserved_slugs;
    }

    /**
     * Build PosttypeModel and initialize hook.
     *
     * @throws PosttypeException
     */
    protected function init() : void
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
     * @return array
     */
    protected function defaultArgs() : array
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
     *
     * @return array
     */
    protected function defaultLabels($plural, $singular) : array
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
     *
     * @return array
     */
    protected function defaultStatuses($name) : array
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
    protected function register() : void
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
     * Set post type arguments.
     *
     * @param  array   $args
     */
    protected function setArgs($args) : void
    {
        if (empty($args)) {
            return;
        }

        $a = $this->getModel()->getArgs();
        $a = array_merge($a, $args);

        $this->getModel()->setArgs($a);
    }

    /**
     * Set post type labels.
     *
     * @param  array   $labels
     */
    protected function setLabels($labels) : void
    {
        if (empty($labels)) {
            return;
        }

        $l = $this->getModel()->getLabels();
        $l = array_merge($l, $labels);

        $this->getModel()->setLabels($l);
    }

    /**
     * Set post type slug.
     *
     * @param  string  $slug
     */
    protected function setSlug($slug) : void
    {
        if (empty($slug)) {
            return;
        }

        $this->getModel()->setSlug(Helpers::urlize($slug));
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars() : void;
}
