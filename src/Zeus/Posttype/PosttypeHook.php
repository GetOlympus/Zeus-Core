<?php

namespace GetOlympus\Zeus\Posttype;

use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Request;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Works with Posttype Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class PosttypeHook
{
    /**
     * @var PostType
     */
    protected $posttype;

    /**
     * Constructor.
     *
     * @param  PostType $posttype
     */
    public function __construct($posttype)
    {
        $slug = $posttype->getModel()->getSlug();

        // Check slug
        if (empty($slug)) {
            return;
        }

        $this->posttype = $posttype;

        // Permalink structures
        add_filter('post_type_link', [$this, 'postTypeLink'], 10, 4);

        if (OL_ZEUS_ISADMIN) {
            // Manage columns
            add_filter('manage_edit-'.$slug.'_columns', [$this, 'manageEditColumns'], 10);
            add_action('manage_'.$slug.'_posts_custom_column', [$this, 'managePostsCustomColumn'], 11, 2);

            // Display post type's custom metaboxes
            add_action('admin_init', [$this, 'postTypeMetaboxesDisplay']);

            // Save post type's custom metaboxes
            add_action('save_post', [$this, 'postTypeSave']);

            // Display settings in permalinks page
            add_action('admin_init', [$this, 'postTypeSettings']);
        }
    }

    /**
     * Hook to change columns on post type list page.
     *
     * @param  array   $columns
     *
     * @return array
     */
    public function manageEditColumns($columns) : array
    {
        // Get current post type
        $current = Request::get('post_type');

        // Check post type
        if (empty($current)) {
            return $columns;
        }

        /**
         * Filter the column headers for a list table on a specific screen.
         *
         * The dynamic portion of the hook name, `$current`, refers to the
         * post type of the current edit screen ID.
         *
         * @var    string  $current
         * @param  array   $columns
         *
         * @return array
         */
        return apply_filters('ol.zeus.posttypehook_manage_edit-'.$current.'_columns', $columns);
    }

    /**
     * Hook to add featured image to column.
     *
     * @param  string  $column
     * @param  int     $post_id
     */
    public function managePostsCustomColumn($column, $post_id) : void
    {
        // Get current post type
        $current = Request::get('post_type');

        // Check post type
        if (empty($current)) {
            return;
        }

        /**
         * Fires for each custom column of a specific post type in the Posts list table.
         *
         * @param  string  $column
         * @param  int     $post_id
         */
        do_action('ol.zeus.posttypehook_manage_'.$current.'_custom_column', $column, $post_id);
    }

    /**
     * Hook building custom permalinks for post types.
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
     *
     * @param  string  $post_link
     * @param  object  $post
     * @param  bool    $leavename
     * @param  bool    $sample
     *
     * @return string
     */
    public function postTypeLink($post_link, $post, $leavename, $sample) : string
    {
        // Define permalink structure
        $rewritecode = [
            '%year%',
            '%monthnum%',
            '%day%',
            '%hour%',
            '%minute%',
            '%second%',
            $leavename ? '' : '%postname%',
            '%post_id%',
            '%category%',
            '%author%',
            $leavename ? '' : '%pagename%',
        ];

        // Need time
        $unixtime = strtotime($post->post_date);
        $date = explode(' ', date('Y m d H i s', $unixtime));

        // Need category
        $category = '';

        // Get categories
        if (strpos($post_link, '%category%') !== false) {
            $cats = get_the_category($post->ID);

            if ($cats) {
                usort($cats, '_usort_terms_by_ID');
                $category = $cats[0]->slug;

                if ($parent = $cats[0]->parent) {
                    $category = get_category_parents($parent, false, '/', true) . $category;
                }
            }

            // Show default category in permalinks, without having to assign it explicitly
            if (empty($category)) {
                $default_category = get_category(Option::get('default_category'));
                $category = is_wp_error($default_category) ? '' : $default_category->slug;
            }
        }

        // Need author
        $author = '';

        // Get authors
        if (strpos($post_link, '%author%') !== false) {
            $authordata = get_userdata($post->post_author);
            $author = $authordata->__get('user_nicename');
        }

        // Define permalink values
        $rewritereplace = [
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post->post_name,
            $post->ID,
            $category,
            $author,
            $post->post_name,
        ];

        // Change structure
        $post_link = str_replace($rewritecode, $rewritereplace, $post_link);

        // Check custom post type with custom taxonomy
        if (!in_array($post->post_type, ['post', 'page'])) {
            $taxs = get_object_taxonomies($post->post_type);

            if (!empty($taxs)) {
                foreach ($taxs as $tax) {
                    $terms = get_the_terms($post->ID, $tax);

                    // Check terms
                    if (!$terms) {
                        continue;
                    }

                    // Sort all
                    $terms = wp_list_sort($terms, 'ID', 'DESC');

                    // Update permalink
                    $post_link = str_replace('%'.$tax.'%', $terms[0]->slug, $post_link);
                }
            }
        }

        // Return permalink
        return $post_link;
    }

    /**
     * Hook building custom metaboxes for Post types.
     */
    public function postTypeMetaboxesDisplay() : void
    {
        $slug = Request::getCurrentSlug();

        // Check slug
        if (empty($slug) || $slug !== $this->posttype->getModel()->getSlug()) {
            return;
        }

        $metaboxes = $this->posttype->getModel()->getMetabox();

        // Check metaboxes
        if (empty($metaboxes)) {
            return;
        }

        // Get metaboxes
        foreach ($metaboxes as $metabox) {
            if (!$metabox) {
                continue;
            }

            // Get contents
            $id     = (string) $metabox->getModel()->getId();
            $title  = (string) $metabox->getModel()->getTitle();
            $fields = (array) $metabox->getModel()->getFields();

            // Check fields
            if (empty($fields)) {
                continue;
            }

            // Title
            $title = empty($title) ? Translate::t('posttypehook.labels.metabox') : $title;
            $id    = empty($id) ? Helpers::urlize($title) : $id;

            // Add meta box
            $metabox->init($slug.'-meta-box-'.$id, $slug);
        }
    }

    /**
     * Hook saving custom fields for Post types.
     *
     * @return bool
     */
    public function postTypeSave() : bool
    {
        global $post;

        if (!isset($post)) {
            return false;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post->ID;
        }

        $slug = $post->post_type;

        // Check slug
        if ($slug !== $this->posttype->getModel()->getSlug()) {
            return false;
        }

        $metaboxes = $this->posttype->getModel()->getMetabox();

        // Check metaboxes
        if (empty($metaboxes)) {
            return false;
        }

        // Remove action hook and add it again later for no infinite loop
        remove_action('save_post', [$this, 'postTypeSave']);

        /**
         * Fires for all post's fields through metaboxes.
         *
         * @var    string  $slug
         * @param  int     $post_id
         * @param  array   $metaboxes
         */
        do_action('ol.zeus.posttypehook_save_'.$slug, $post->ID, $metaboxes);

        // Update all metas
        foreach ($metaboxes as $metabox) {
            if (!$metabox) {
                continue;
            }

            // Get contents
            $title  = (string) $metabox->getModel()->getTitle();
            $fields = (array) $metabox->getModel()->getFields();

            // Check fields
            if (empty($fields)) {
                continue;
            }

            // Get all values
            foreach ($fields as $field) {
                if (!$field) {
                    continue;
                }

                $id = (string) $field->getModel()->getIdentifier();

                if (empty($id)) {
                    continue;
                }

                // Gets the value
                $value = Request::post($id, null);

                $post_id = $post->ID;
                $option_name = $slug.'-'.$id;

                // Check value
                if (is_null($value)) {
                    $value = Option::getPostMeta($post_id, $option_name);
                }

                /**
                 * Filter the value content.
                 *
                 * @var    string  $slug
                 * @param  object  $value
                 * @param  int     $post_id
                 * @param  string  $option_name
                 *
                 * @return object
                 */
                $value = apply_filters('ol.zeus.posttypehook_save_'.$slug.'_field', $value, $post_id, $option_name);

                // Updates meta
                Option::updatePostMeta($post_id, $option_name, $value);
            }
        }

        // Add back action hook again for no infinite loop
        add_action('save_post', [$this, 'postTypeSave']);

        return true;
    }

    /**
     * Hook building custom options in Permalink settings page.
     *
     * @return bool
     */
    public function postTypeSettings() : bool
    {
        // Add section
        add_settings_section(
            'olympus-permalinks',
            Translate::t('posttypehook.labels.custom_permalinks'),
            [$this, 'postTypeSettingTitle'],
            'permalink'
        );

        // Flush all rewrite rules
        if (isset($_POST['flushpermalink'])) {
            flush_rewrite_rules();
        }

        $slug = $this->posttype->getModel()->getSlug();

        // Special case: do not change post/page component
        if (in_array($slug, $this->posttype->getReservedSlugs())) {
            return false;
        }

        // Option
        $opt = 'permalink_structure_'.$slug;
        $val = Request::post($opt, '');

        // Check POST
        $value = !empty($val) ? $val : Option::get($opt, '/%'.$slug.'%-%post_id%');

        if (!empty($value)) {
            Option::set($opt, $value);
        }

        $args = $this->posttype->getModel()->getArgs();
        $name = isset($args['labels']['name']) ? $args['labels']['name'] : '';

        // Define metabox title
        $title = $name.' <code>%'.$slug.'%</code>';

        // Add fields
        add_settings_field(
            $opt,
            $title,
            [$this, 'postTypeSettingFunc'],
            'permalink',
            'olympus-permalinks',
            [
                'name'   => $opt,
                'value'  => $value,
            ]
        );

        return true;
    }

    /**
     * Hook to display input value on Permalink settings page.
     *
     * @param  array   $vars
     */
    public function postTypeSettingFunc($vars) : void
    {
        if (empty($vars)) {
            return;
        }

        $vars['t_description'] = Translate::t('posttypehook.labels.description');
        $vars['t_home'] = OL_ZEUS_HOME;

        // Render view
        $render = new Render('core', 'layouts'.S.'permalinks.html.twig', $vars, []);
        $render->view();
    }

    /**
     * Hook to display hidden input on Permalink settings title page.
     */
    public function postTypeSettingTitle() : void
    {
        // Display settings
        $this->postTypeSettingFunc([
            'name'  => 'flushpermalink',
            'value' => '1',
        ]);
    }
}
