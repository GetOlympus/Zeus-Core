<?php

namespace GetOlympus\Zeus\Posttype\Controller;

use GetOlympus\Zeus\Common\Controller\Common;
use GetOlympus\Zeus\Field\Controller\Field;
use GetOlympus\Zeus\Metabox\Controller\Metabox;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Posttype\Controller\Posttype;
use GetOlympus\Zeus\Posttype\Controller\PosttypeHookInterface;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Works with Posttype Engine.
 *
 * @package Olympus Zeus-Core
 * @subpackage Posttype\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class PosttypeHook implements PosttypeHookInterface
{
    /**
     * @var array
     */
    protected $metaboxes;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $reserved_slugs;

    /**
     * @var string
     */
    protected $slug;

    /**
     * Constructor.
     *
     * @param string    $slug
     * @param string    $name
     * @param array     $metaboxes
     * @param array     $reserved_slugs
     */
    public function __construct($slug, $name, $metaboxes = [], $reserved_slugs = [])
    {
        // Check slug
        if (empty($slug)) {
            return;
        }

        $this->slug = $slug;
        $this->name = $name;
        $this->metaboxes = $metaboxes;
        $this->reserved_slugs = $reserved_slugs;

        // Permalink structures
        add_filter('post_type_link', [&$this, 'postTypeLink'], 10, 4);

        if (OL_ZEUS_ISADMIN) {
            // Manage columns
            add_filter('manage_edit-'.$slug.'_columns', [&$this, 'manageEditColumns'], 10);
            add_action('manage_'.$slug.'_posts_custom_column', [&$this, 'managePostsCustomColumn'], 11, 2);

            // Display post type's custom metaboxes
            add_action('admin_init', [&$this, 'postTypeMetaboxesDisplay']);

            // Save post type's custom metaboxes
            add_action('save_post', [&$this, 'postTypeSave']);

            // Display settings in permalinks page
            add_action('admin_init', [&$this, 'postTypeSettings']);
        }
    }

    /**
     * Hook to change columns on post type list page.
     *
     * @param array $columns
     * @return array $columns
     */
    public function manageEditColumns($columns)
    {
        // Get current post type
        $current = Request::get('post_type');

        // check post type
        if (empty($current)) {
            return $columns;
        }

        /**
         * Filter the column headers for a list table on a specific screen.
         *
         * The dynamic portion of the hook name, `$current`, refers to the
         * post type of the current edit screen ID.
         *
         * @var string $current
         * @param array $columns
         * @return array $columns
         */
        return apply_filters('ol_zeus_posttypehook_manage_edit-'.$current.'_columns', $columns);
    }

    /**
     * Hook to add featured image to column.
     *
     * @param string $column
     * @param integer $post_id
     */
    public function managePostsCustomColumn($column, $post_id)
    {
        // Get current post type
        $current = Request::get('post_type');

        // check post type
        if (empty($current)) {
            return;
        }

        /**
         * Fires for each custom column of a specific post type in the Posts list table.
         *
         * @param string $column
         * @param int $post_id
         */
        do_action('ol_zeus_posttypehook_manage_'.$current.'_custom_column', $column, $post_id);
    }

    /**
     * Hook building custom permalinks for post types.
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
     *
     * @param   string  $post_link
     * @param   object  $post
     * @param   boolean $leavename
     * @param   boolean $sample
     * @return  string  $permalink
     */
    public function postTypeLink($post_link, $post, $leavename, $sample)
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
        $date = explode(' ', date(Translate::t('posttypehook.datetime'), $unixtime));

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
    public function postTypeMetaboxesDisplay()
    {
        // Check metaboxes
        if (empty($this->metaboxes)) {
            return;
        }

        // Defintions
        $slug = Request::getCurrentSlug();

        // Check slug
        if (empty($slug) || $slug !== $this->slug) {
            return;
        }

        // Get metaboxes
        foreach ($this->metaboxes as $metabox) {
            if (!$metabox) {
                continue;
            }

            // Get contents
            $id = (integer) $metabox->getModel()->getId();
            $title = (string) $metabox->getModel()->getTitle();
            $fields = (array) $metabox->getModel()->getFields();

            // Check fields
            if (empty($fields)) {
                continue;
            }

            // Title
            $title = empty($title) ? Translate::t('posttypehook.metabox') : $title;
            $id = empty($id) ? Common::urlize($title) : $id;

            // Update vars
            $identifier = $slug.'-meta-box-'.$id;

            // Add meta box
            $metabox->init($identifier, $slug);
        }
    }

    /**
     * Hook saving custom fields for Post types.
     */
    public function postTypeSave()
    {
        global $post;

        if (!isset($post)) {
            return false;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post->ID;
        }

        // Get contents
        $slug = $post->post_type;

        // Check metaboxes and slug
        if (empty($this->metaboxes) || $slug !== $this->slug) {
            return;
        }

        // Remove action hook and add it again later for no infinite loop
        remove_action('save_post', [&$this, 'postTypeSave']);

        /**
         * Fires for all post's fields through metaboxes.
         *
         * @var string $slug
         * @param int $post_id
         * @param array $metaboxes
         */
        do_action('ol_zeus_posttypehook_save_'.$slug, $post->ID, $this->metaboxes);

        // Update all metas
        foreach ($this->metaboxes as $metabox) {
            if (!$metabox) {
                continue;
            }

            // Get contents
            $id = (integer) $metabox->getModel()->getId();
            $title = (string) $metabox->getModel()->getTitle();
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

                // Build contents
                $ctn = (array) $field->getModel()->getContents();
                $hasId = (boolean) $field->getModel()->getHasId();

                // Check ID
                if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                    continue;
                }

                // Gets the value
                $value = Request::post($ctn['id'], null);

                // Check value
                if (is_null($value)) {
                    $value = Option::getPostMeta($post->ID, $slug.'-'.$ctn['id']);
                }

                /**
                 * Filter the value content.
                 *
                 * @var string $current
                 * @param int $post_id
                 * @param string $option_name
                 * @param object $value
                 * @return object $value
                 */
                $value = apply_filters('ol_zeus_posttypehook_save_'.$slug.'_field', $value, $post->ID, $slug.'-'.$ctn['id']);

                // Updates meta
                Option::updatePostMeta($post->ID, $slug.'-'.$ctn['id'], $value);
            }
        }

        // Add back action hook again for no infinite loop
        add_action('save_post', [&$this, 'postTypeSave']);

        return true;
    }

    /**
     * Hook building custom options in Permalink settings page.
     */
    public function postTypeSettings()
    {
        // Add section
        add_settings_section(
            'olympus-permalinks',
            Translate::t('posttypehook.custom_permalinks'),
            [&$this,'postTypeSettingTitle'],
            'permalink'
        );

        // Flush all rewrite rules
        if (isset($_POST['flushpermalink'])) {
            flush_rewrite_rules();
        }

        // Special case: do not change post/page component
        if (in_array($this->slug, $this->reserved_slugs)) {
            return false;
        }

        // Option
        $opt = 'permalink_structure_'.$this->slug;

        // Check POST
        if (isset($_POST[$opt])) {
            $value = $_POST[$opt];
            Option::set($opt, $value);
        } else {
            $value = Option::get($opt, '/%'.$this->slug.'%-%post_id%');
        }

        // Define metabox title
        $title = $this->name.' <code>%'.$this->slug.'%</code>';

        // Add fields
        add_settings_field(
            $opt,
            $title,
            [$this, 'postTypeSettingFunc'],
            'permalink',
            'olympus-permalinks',
            [
                'name' => $opt,
                'value' => $value,
            ]
        );

        return true;
    }

    /**
     * Hook to display input value on Permalink settings page.
     *
     * @param array $vars
     */
    public function postTypeSettingFunc($vars)
    {
        if (empty($vars)) {
            return;
        }

        $vars['t_description'] = Translate::t('posttypehook.description');
        $vars['t_home'] = OL_ZEUS_HOME;

        // Render template
        Render::view('permalinks.html.twig', $vars, 'posttype');
    }

    /**
     * Hook to display hidden input on Permalink settings title page.
     */
    public function postTypeSettingTitle()
    {
        // Display settings
        $this->postTypeSettingFunc([
            'name' => 'flushpermalink',
            'value' => '1',
        ]);
    }
}
