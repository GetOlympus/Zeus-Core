<?php

namespace GetOlympus\Hera\Term\Controller;

use GetOlympus\Hera\Notification\Controller\Notification;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Term\Controller\TermHook;
use GetOlympus\Hera\Term\Controller\TermInterface;
use GetOlympus\Hera\Term\Exception\TermException;
use GetOlympus\Hera\Term\Model\TermModel;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Gets its own term.
 *
 * @package Olympus Hera
 * @subpackage Term\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

abstract class Term implements TermInterface
{
    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    protected $args;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $forbidden_slugs = ['attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category__and', 'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'customize_messenger_channel', 'customized', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence', 'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'theme', 'type', 'w', 'withcomments', 'withoutcomments', 'year'];

    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    protected $labels;

    /**
     * @var array
     */
    protected $reserved_slugs = ['category', 'post_tag'];

    /**
     * @var string
     */
    protected $posttype;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var TermModel
     */
    protected $term;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Update slug
        $this->slug = Render::urlize($this->slug);

        // Check forbidden slugs
        if (in_array($this->slug, $this->forbidden_slugs)) {
            throw new TermException(Translate::t('term.errors.slug_is_forbidden'));
        }

        // Check post type association
        $this->posttype = empty($this->posttype) ? 'post' : $this->posttype;

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Build TermModel and initialize hook.
     */
    public function init()
    {
        // Initialize TermModel
        $this->term = new TermModel();
        $this->term->setFields($this->fields);
        $this->term->setPosttype($this->posttype);
        $this->term->setSlug($this->slug);

        // Update args on terms except reserved ones
        if (!in_array($this->slug, $this->reserved_slugs)) {
            // Check if term already exists
            if (term_exists($this->slug)) {
                throw new TermException(Translate::t('term.errors.slug_already_exists'));
            }

            // Initialize plural and singular vars
            $this->labels['name'] = isset($this->labels['name']) ? $this->labels['name'] : '';
            $this->labels['singular_name'] = isset($this->labels['singular_name']) ? $this->labels['singular_name'] : '';

            // Check label for all except reserved ones
            if (empty($this->labels['name']) || empty($this->labels['singular_name'])) {
                throw new TermException(Translate::t('term.errors.missing_singular_or_plural'));
            }

            $this->args = array_merge($this->defaultArgs(), $this->args);
            $this->args['labels'] = array_merge(
                $this->defaultLabels($this->labels['name'], $this->labels['singular_name']),
                $this->labels
            );

            // Update TermModel args
            $this->term->setArgs($this->args);
        }

        // Register term
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
        $slug = $this->term->getSlug();

        // Return args
        return [
            'hierarchical' => true,
            'query_var' => true,
            'rewrite' => ['slug' => $slug],
            'show_admin_column' => true,
            'show_ui' => true,

            'show_in_rest' => true,
            'rest_base' => $slug,
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ];
    }

    /**
     * Build labels.
     *
     * @param string $plural
     * @param string $singular
     * @param boolean $hierarchical
     * @return array $labels
     */
    public function defaultLabels($plural, $singular, $hierarchical = true)
    {
        $labels = [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => $plural,

            'search_items' => Translate::t('term.labels.search_items'),
            'edit_item' => Translate::t('term.labels.edit_item'),
            'update_item' => Translate::t('term.labels.update_item'),
            'add_new_item' => Translate::t('term.labels.add_new_item'),
            'new_item_name' => Translate::t('term.labels.new_item_name'),
        ];

        if ($hierarchical) {
            $labels = array_merge($labels, [
                'parent_item' => Translate::t('term.labels.parent_item'),
                'parent_item_colon' => Translate::t('term.labels.parent_item_colon'),
            ]);
        } else {
            $labels = array_merge($labels, [
                'parent_item' => null,
                'parent_item_colon' => null,

                'popular_items' => Translate::t('term.labels.popular_items'),
                'separate_items_with_commas' => Translate::t('term.labels.separate_items_with_commas'),
                'choose_from_most_used' => Translate::t('term.labels.choose_from_most_used'),

                'add_or_remove_items' => Translate::t('term.labels.add_or_remove_items'),
                'not_found' => Translate::t('term.labels.not_found'),
            ]);
        }

        return $labels;
    }

    /**
     * Register post types.
     */
    public function register()
    {
        // Store details
        $slug = $this->term->getSlug();
        $args = $this->term->getArgs();
        $fields = $this->term->getFields();
        $posttype = $this->term->getPosttype();

        $is_single = 'single' === $args['choice'] ? true : false;

        // Register post type if not post or page
        if (!in_array($slug, $this->reserved_slugs)) {
            register_taxonomy($slug, $posttype, $args);
        }

        // Works on hook
        $hook = new TermHook($slug, $posttype, $fields, $is_single);
        $this->term->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
