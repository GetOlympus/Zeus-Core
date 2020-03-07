<?php

namespace GetOlympus\Zeus\Term;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Term\TermHook;
use GetOlympus\Zeus\Term\TermInterface;
use GetOlympus\Zeus\Term\TermException;
use GetOlympus\Zeus\Term\TermModel;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own term.
 *
 * @package    OlympusZeusCore
 * @subpackage Term
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Term extends Base implements TermInterface
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $forbidden_slugs = [
        'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category__and', 'category__in',
        'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'customize_messenger_channel',
        'customized', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute',
        'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page',
        'page_id', 'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format',
        'post_mime_type', 'post_status', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview',
        'robots', 's', 'search', 'second', 'sentence', 'showposts', 'static', 'subpost', 'subpost_id', 'tag',
        'tag__and', 'tag__in', 'tag__not_in', 'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term',
        'theme', 'type', 'w', 'withcomments', 'withoutcomments', 'year'
    ];

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
    protected $reserved_slugs = ['category', 'post_tag'];

    /**
     * @var string
     */
    protected $singular_name = '';

    /**
     * @var string
     */
    protected $posttype = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize TermModel
        $this->model = new TermModel();

        // Update model
        $this->setSlug($this->slug);
        $this->setPosttype($this->posttype);
        $this->setArgs(array_merge([
            'menu_icon'  => 'dashicons-dashboard',
            'supports'   => ['title'],
            'taxonomies' => [],
        ], $this->args));
        $this->setLabels(array_merge([
            'name'          => $this->name,
            'singular_name' => $this->singular_name,
        ], $this->labels));

        // Add fields and more
        $this->setVars();
        $this->init();
    }

    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws TermException
     */
    public function addFields($fields) : void
    {
        // Check fields
        if (empty($fields)) {
            throw new TermException(Translate::t('term.errors.fields_are_not_defined'));
        }

        // Update fields
        $this->getModel()->setFields($fields);
    }

    /**
     * Return term reserved slugs.
     *
     * @return array
     */
    protected function getReservedSlugs() : array
    {
        return $this->reserved_slugs;
    }

    /**
     * Build TermModel and initialize hook.
     *
     * @throws TermException
     */
    protected function init() : void
    {
        $slug = $this->getModel()->getSlug();

        // Check forbidden slugs
        if (in_array($slug, $this->forbidden_slugs)) {
            throw new TermException(Translate::t('term.errors.slug_is_forbidden'));
        }

        // Check post type association
        $posttype = $this->getModel()->getPosttype();

        // Association with post by default
        if (!$posttype) {
            $this->getModel()->setPosttype('post');
        }

        // Update args on post types except reserved ones
        if (!in_array($slug, $this->reserved_slugs)) {
            // Check if term already exists
            if (term_exists($slug)) {
                throw new TermException(Translate::t('term.errors.slug_already_exists'));
            }

            $args   = $this->getModel()->getArgs();
            $labels = $this->getModel()->getLabels();

            // Initialize plural and singular vars
            $labels['name']          = isset($labels['name']) ? $labels['name'] : '';
            $labels['singular_name'] = isset($labels['singular_name']) ? $labels['singular_name'] : '';

            // Check label for all except reserved ones
            if (empty($labels['name']) || empty($labels['singular_name'])) {
                throw new TermException(Translate::t('term.errors.missing_singular_or_plural'));
            }

            $args   = array_merge($this->defaultArgs(), $args);
            $labels = array_merge($this->defaultLabels($labels['name'], $labels['singular_name']), $labels);

            $args['labels'] = $labels;

            // Update args and labels
            $this->getModel()->setArgs($args);
            $this->getModel()->setLabels($labels);
        }

        // Register term
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
            'choice'                => 'multiple',
            'hierarchical'          => true,
            'query_var'             => true,
            'rewrite'               => [
                'slug' => $this->getModel()->getSlug()
            ],
            'show_admin_column'     => true,
            'show_ui'               => true,

            'show_in_rest'          => true,
            'rest_base'             => $this->getModel()->getSlug(),
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ];
    }

    /**
     * Build labels.
     *
     * @param  string  $plural
     * @param  string  $singular
     * @param  bool    $hierarchical
     *
     * @return array
     */
    protected function defaultLabels($plural, $singular, $hierarchical = true) : array
    {
        $labels = [
            'name'          => $plural,
            'singular_name' => $singular,
            'menu_name'     => $plural,
            'all_items'     => $plural,

            'search_items'  => Translate::t('term.labels.search_items'),
            'edit_item'     => Translate::t('term.labels.edit_item'),
            'update_item'   => Translate::t('term.labels.update_item'),
            'add_new_item'  => Translate::t('term.labels.add_new_item'),
            'new_item_name' => Translate::t('term.labels.new_item_name'),
        ];

        if ($hierarchical) {
            $labels = array_merge($labels, [
                'parent_item'       => Translate::t('term.labels.parent_item'),
                'parent_item_colon' => Translate::t('term.labels.parent_item_colon'),
            ]);
        } else {
            $labels = array_merge($labels, [
                'parent_item'       => null,
                'parent_item_colon' => null,

                'popular_items'              => Translate::t('term.labels.popular_items'),
                'separate_items_with_commas' => Translate::t('term.labels.separate_items_with_commas'),
                'choose_from_most_used'      => Translate::t('term.labels.choose_from_most_used'),

                'add_or_remove_items' => Translate::t('term.labels.add_or_remove_items'),
                'not_found'           => Translate::t('term.labels.not_found'),
            ]);
        }

        return $labels;
    }

    /**
     * Register term.
     */
    protected function register() : void
    {
        // Store details
        $slug = $this->getModel()->getSlug();

        // Register post type if not post or page
        if (!in_array($slug, $this->reserved_slugs)) {
            register_taxonomy($slug, $this->getModel()->getPosttype(), $this->getModel()->getArgs());
        }

        // Works on hook
        new TermHook($this);
    }

    /**
     * Set term arguments.
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
     * Set term labels.
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
     * Set term post type.
     *
     * @param  string  $posttype
     */
    protected function setPosttype($posttype) : void
    {
        if (empty($posttype)) {
            return;
        }

        $this->getModel()->setPosttype($posttype);
    }

    /**
     * Set term slug.
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
