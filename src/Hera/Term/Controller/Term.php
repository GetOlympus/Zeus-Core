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
 * Gets its own post type.
 *
 * @package Olympus Hera
 * @subpackage Term\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Term implements TermInterface
{
    /**
     * @var array
     */
    protected static $existingTerms = ['attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and', 'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'customize_messenger_channel', 'customized', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence', 'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'theme', 'type', 'w', 'withcomments', 'withoutcomments', 'year'];

    /**
     * @var TermModel
     */
    protected $term;

    /**
     * Initialization.
     *
     * @param string $slug
     * @param array $args
     * @param array $labels
     */
    public function init($slug, $posttype, $args, $labels)
    {
        if (empty($labels) || !isset($labels['plural'], $labels['singular']) || empty($labels['plural']) || empty($labels['singular'])) {
            throw new PosttypeException(Translate::t('term.errors.missing_singular_or_plural'));
        }

        if (empty($posttype)) {
            throw new PosttypeException(Translate::t('term.errors.posttype_not_defined'));
        }

        $this->term = new TermModel();

        $slug = Render::urlize($slug);
        $args = array_merge($this->defaultArgs($slug), $args);
        $args['labels'] = array_merge($this->defaultLabels($labels['plural'], $labels['singular'], $args['hierarchical']), $labels);

        // Update vars
        $this->term->setSlug($slug);
        $this->term->setPosttype($posttype);
        $this->term->setArgs($args);

        // Hooks
        if (OLH_ISADMIN) {
            add_filter('olh_template_footer_urls', function ($urls, $identifier) {
                return array_merge($urls, [
                    'terms' => [
                        'url' => admin_url('admin.php?page='.$identifier.'&do=olz-action&from=footer&make=terms'),
                        'label' => Translate::t('term.title'),
                    ]
                ]);
            }, 10, 2);
        }
    }

    /**
     * Build args.
     *
     * @param string $slug
     * @return array $args
     */
    public function defaultArgs($slug)
    {
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
        }
        else {
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
        add_action('init', function (){
            // Store details
            $slug = $this->term->getSlug();
            $posttype = $this->term->getPosttype();
            $args = $this->term->getArgs();

            $issingle = 'single' === $args['choice'] ? true : false;
            $addcustomfields = false;

            // Check datum
            if (empty($slug) || empty($posttype) || empty($args)) {
                return [];
            }

            // Check forbodden keywords and already existing terms
            if (in_array($slug, self::$existingTerms) || taxonomy_exists($slug)) {
                return [];
            }

            // Action to register
            register_taxonomy($slug, $posttype, $args);

            // Works on hook
            $hook = new TermHook($slug, $addcustomfields, $issingle);
            $this->term->setHook($hook);
        }, 10, 1);
    }
}
