<?php

namespace GetOlympus\Hera\Posttype\Controller;

use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Metabox\Controller\Metabox;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Posttype\Controller\Posttype;
use GetOlympus\Hera\Posttype\Controller\PosttypeHookInterface;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Works with Posttype Engine.
 *
 * @package Olympus Hera
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
    protected $fields;

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
     * @param array     $fields
     * @param array     $reserved_slugs
     */
    public function __construct($slug, $name, $fields = [], $reserved_slugs = [])
    {
        // Check slug
        if (empty($slug)) {
            return;
        }

        $this->slug = $slug;
        $this->name = $name;
        $this->fields = $fields;
        $this->reserved_slugs = $reserved_slugs;

        // Permalink structures
        add_action('post_type_link', [&$this, 'postTypeLink'], 10, 3);

        if (OLH_ISADMIN) {
            // Manage columns
            add_filter('manage_edit-'.$slug.'_columns', [&$this, 'manageEditColumns'], 10);
            add_action('manage_'.$slug.'_posts_custom_column', [&$this, 'managePostsCustomColumn'], 11, 2);

            // Display post type's custom fields
            add_action('admin_init', [&$this, 'postTypeFieldDisplay']);

            // Save post type's custom fields
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
        return apply_filters('olh_posttypehook_manage_edit-'.$current.'_columns', $columns);
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
        do_action('olh_posttypehook_manage_'.$current.'_custom_column', $column, $post_id);
    }

    /**
     * Hook building custom permalinks for post types.
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
     *
     * @param string $permalink
     * @param integer $post_id
     * @param boolean $leavename
     * @return string $permalink
     */
    public function postTypeLink($permalink, $post_id, $leavename)
    {
        if (!$post_id) {
            return '';
        }

        // Get post's datas
        $post = get_post($post_id);

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

        if ('' === $permalink || in_array($post->post_status, ['draft', 'pending', 'auto-draft'])) {
            return $permalink;
        }

        // Need time
        $unixtime = strtotime($post->post_date);
        $date = explode(' ', date(Translate::t('posttypehook.datetime'), $unixtime));

        // Need category
        $category = '';

        // Get categories
        if (strpos($permalink, '%category%') !== false) {
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
        if (strpos($permalink, '%author%') !== false) {
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
        $permalink = str_replace($rewritecode, $rewritereplace, $permalink);

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
                    usort($terms, '_usort_terms_by_ID');

                    // Update permalink
                    $permalink = str_replace('%'.$tax.'%', $terms[0]->slug, $permalink);
                }
            }
        }

        // Return permalink
        return $permalink;
    }

    /**
     * Hook building custom fields for CPTS.
     */
    public function postTypeFieldDisplay()
    {
        // Check fields
        if (empty($this->fields)) {
            return;
        }

        // Defintions
        $slug = Request::getCurrentSlug();

        // Check slug
        if (empty($slug) || $slug !== $this->slug) {
            return;
        }

        // Get fields
        foreach ($this->fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            $ctn = (array) $field->getField()->getContents();
            $hasId = (boolean) $field->getField()->getHasId();

            // Check fields
            if (empty($ctn)) {
                continue;
            }

            // Does the field have an ID
            if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                continue;
            }

            // Id, with a random ID when it's needed
            $id = isset($ctn['id']) ? $ctn['id'] : rand(777, 7777777);

            // Title
            $title = isset($ctn['title']) ? $ctn['title'] : Translate::t('posttypehook.metabox');

            // Update vars
            $identifier = $slug.'-meta-box-'.$id;

            // Add meta box
            $metabox = new Metabox();
            $metabox->init($identifier, $slug, $title, [
                'field' => $field
            ]);
        }
    }

    /**
     * Hook building custom fields for Post types.
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

        // Check fields and slug
        if (empty($this->fields) || $slug !== $this->slug) {
            return;
        }

        // Update all metas
        foreach ($this->fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            $ctn = (array) $field->getField()->getContents();
            $hasId = (boolean) $field->getField()->getHasId();

            // Check ID
            if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                continue;
            }

            $value = Request::post($ctn['id']);
            Option::updatePostMeta($post->ID, $slug.'-'.$ctn['id'], $value);
        }

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
        $opt = str_replace('%SLUG%', $this->slug, '/%'.$this->slug.'%-%post_id%');

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
        $vars['t_home'] = OLH_HOME;

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
