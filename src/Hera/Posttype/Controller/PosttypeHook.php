<?php

namespace GetOlympus\Hera\Posttype\Controller;

use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Metabox\Controller\Metabox;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Works with Posttype Engine.
 *
 * @package Olympus Hera
 * @subpackage PosttypeHook\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class PosttypeHook
{
    /**
     * Constructor.
     *
     * @param string $slug
     */
    public function __construct($slug)
    {
        // Manage columns
        if (OLH_ISADMIN && !empty($slug)) {
            add_filter('manage_edit-'.$slug.'_columns', [&$this->hook, 'manageEditColumns'], 10);
            add_action('manage_'.$slug.'_posts_custom_column', [&$this->hook, 'managePostsCustomColumn'], 11, 2);
        }

        // Permalink structures
        add_action('post_type_link', [&$this->hook, 'postTypeLink'], 10, 3);

        if (OLH_ISADMIN) {
            // Display post type's custom fields
            add_action('admin_init', [&$this->hook, 'postTypeFieldDisplay']);

            // Save post type's custom fields
            add_action('save_post', [&$this->hook, 'postTypeSave']);

            // Display settings in permalinks page
            add_action('admin_init', [&$this->hook, 'postTypeSettings']);
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
        // Defintions
        $slug = Request::get('post_type');
        $usedIds = [];

        // Define current post type's contents
        if (empty($slug)) {
            $post = Request::get('post', 0);
            $slug = !empty($post) ? get_post_type($post) : '';

            // Define pagenow var
            if (empty($slug)) {
                global $pagenow;

                if ('post-new.php' === $pagenow) {
                    $slug = 'post';
                }
                else if ('media-new.php' === $pagenow) {
                    $slug = 'attachment';
                }
                else {
                    return;
                }
            }
        }

        /**
         * Build post type contents.
         *
         * @var string $slug
         * @param array $contents
         * @return array $contents
         */
        $contents = apply_filters('olh_posttypehook_'.$slug.'_contents', []);

        // Check contents
        if (empty($contents)) {
            return;
        }

        // Get contents
        foreach ($contents as $ctn) {
            // Check fields
            if (empty($ctn)) {
                continue;
            }

            // Get type and id
            $type = isset($ctn['type']) ? $ctn['type'] : '';
            $id = isset($ctn['id']) ? $ctn['id'] : '';

            // Check if we are authorized to use this field in CPTs
            if (empty($type)) {
                continue;
            }

            // Title
            $title = isset($ctn['title']) ? $ctn['title'] : Translate::t('posttypehook.metabox');

            // Get field instance
            $field = Field::build($type, $id, $usedIds);

            // Check error
            if (is_array($field) && $field['error']) {
                continue;
            }

            // Update ids
            if (!empty($id)) {
                $usedIds[] = $id;
            }

            // Update vars
            $identifier = $slug.'-meta-box-'.$id;

            // Add meta box
            new Metabox($identifier, $slug, $title, [
                'type' => $type,
                'field' => $field,
                'contents' => $ctn
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

        /**
         * Build post type contents.
         *
         * @var string $slug
         * @param array $contents
         * @return array $contents
         */
        $contents = apply_filters('olh_posttypehook_'.$slug.'_contents', []);

        // Check contents
        if (empty($contents)) {
            return false;
        }

        // Update all metas
        foreach ($contents as $ctn) {
            $value = Request::get($ctn['id']);
            update_post_meta($post->ID, $post->post_type.'-'.$ctn['id'], $value);
        }

        return true;
    }

    /**
     * Hook building custom options in Permalink settings page.
     */
    public function postTypeSettings()
    {
        /**
         * Build post type contents.
         *
         * @var string $slug
         * @param array $contents
         * @return array $contents
         */
        $contents = apply_filters('olh_posttypehook_'.$slug.'_contents', []);

        // Check contents
        if (empty($contents)) {
            return false;
        }

        // Add section
        add_settings_section(
            'olh-permalinks',
            Translate::t('posttypehook.custom_permalinks'),
            [&$this,'postTypeSettingTitle'],
            'permalink'
        );

        // Flush all rewrite rules
        if (isset($_POST['olh-flushpermalink'])) {
            flush_rewrite_rules();
        }

        // Iterate on each cpt
        foreach ($this->posttypes as $pt) {
            // Special case: do not change post/page component
            if (in_array($pt['slug'], ['post', 'page'])) {
                continue;
            }

            // Option
            $opt = str_replace('%SLUG%', $pt['slug'], '/%'.$pt['slug'].'%-%post_id%');

            // Check POST
            if (isset($_POST[$opt])) {
                $value = $_POST[$opt];
                Option::set($opt, $value);
            }
            else {
                $value = Option::get($opt, '/%'.$pt['slug'].'%-%post_id%');
            }

            // Define metabox title
            $title = $pt['labels']['name'].' <code>%'.$pt['slug'].'%</code>';

            // Add fields
            add_settings_field(
                $opt,
                $title,
                [&$this,'postTypeSettingFunc'],
                'permalink',
                'olz-permalinks',
                [
                    'name' => $opt,
                    'value' => $value,
                ]
            );
        }

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
        Render::view('Layouts/permalinks.html.twig', $vars, 'posttype');
    }

    /**
     * Hook to display hidden input on Permalink settings title page.
     */
    public function postTypeSettingTitle()
    {
        // Display settings
        $this->postTypeSettingFunc([
            'name' => 'olh-flushpermalink',
            'value' => '1',
        ]);
    }
}
