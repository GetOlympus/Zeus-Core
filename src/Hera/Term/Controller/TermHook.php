<?php

namespace GetOlympus\Hera\Term\Controller;

use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Term\Controller\TermHookInterface;
use GetOlympus\Hera\WalkerSingle\Controller\WalkerSingle;

/**
 * Works with Term Engine.
 *
 * @package Olympus Hera
 * @subpackage Term\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class TermHook implements TermHookInterface
{
    /**
     * Constructor.
     *
     * @param string $slug
     * @param boolean $addCustomFields
     * @param boolean $isSingle
     */
    public function __construct($slug, $addCustomFields = false, $isSingle = false)
    {
        // Custom fields with custom columns
        if (OLH_ISADMIN && !empty($slug)) {
            // Edit custom fields
            add_action($slug.'_edit_form_fields', [&$this, 'editFormFields'], 10, 1);

            // Add custom fields
            if ($addCustomFields) {
                add_action($slug.'_add_form_fields', [&$this, 'editFormFields'], 10, 1);
            }

            // Save custom fields
            add_action('created_'.$slug, [&$this, 'saveFields'], 10, 2);
            add_action('edited_'.$slug, [&$this, 'saveFields'], 10, 2);

            // Display custom columns
            add_filter('manage_edit-'.$slug.'_columns', [&$this, 'manageEditColumns'], 10);
            add_action('manage_'.$slug.'_custom_column', [&$this, 'manageCustomColumn'], 11, 3);
        }

        // Special case: single choice on post edit page
        if (OLH_ISADMIN && $isSingle && !empty($slug)) {
            // Apply filter
            add_filter('wp_terms_checklist_args', function ($args, $post_id) use ($slug) {
                // Check taxonomy
                if (isset($args['taxonomy']) && $slug === $args['taxonomy']) {
                    $args['walker'] = new WalkerSingle();
                    $args['popular_cats'] = [];
                    $args['checked_ontop'] = false;
                }

                return $args;
            }, 10, 2);
        }
    }

    /**
     * Hook building custom fields.
     *
     * @param string|object $term
     */
    public function editFormFields($term)
    {
        // Definitions
        $usedIds = [];

        // Get current
        $isobject = is_object($term);
        $slug = $isobject ? $term->taxonomy : $term;
        $termid = $isobject ? $term->term_id : 0;

        /**
         * Build term contents.
         *
         * @var string $slug
         * @param array $contents
         * @return array $contents
         */
        $contents = apply_filters('olh_termhook_'.$slug.'_contents', []);

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

            // Get field instance
            $field = Field::build($type, $id, $usedIds);

            // Update ids
            if (!empty($id)) {
                $usedIds[] = $id;
            }

            // Get template
            $tpl = $field->render($ctn, [
                'prefix' => $slug,
                'term_id' => $termid,
                'structure' => '%TERM%-%SLUG%'
            ]);
        }
    }

    /**
     * Hook to add custom column.
     *
     * @param string $content
     * @param string $column
     * @param int $term_id
     */
    public function manageCustomColumn($content, $column, $term_id)
    {
        // Get current post type
        $current = Request::get('taxonomy');

        // check post type
        if (empty($current)) {
            return;
        }

        /**
         * Fires for each custom column of a specific post type in the Posts list table.
         *
         * @param string $content
         * @param string $column
         * @param int $term_id
         */
        do_action('olh_termhook_manage_'.$current.'_custom_column', $content, $column, $term_id);
    }

    /**
     * Hook to change columns on term list page.
     *
     * @param array $columns
     * @return array $columns
     */
    public function manageEditColumns($columns)
    {
        // Get current post type
        $current = Request::get('taxonomy');

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
        return apply_filters('olh_termhook_manage_edit-'.$current.'_columns', $columns);
    }

    /**
     * Hook building custom fields for Post types.
     *
     * @param number $term_id
     * @return number|void
     */
    public function saveFields($term_id)
    {
        // Admin panel
        if (!OLH_ISADMIN) {
            return;
        }

        // Check term
        if (!isset($term_id) || empty($term_id)) {
            return;
        }

        // Get all requests
        $request = isset($_REQUEST) ? $_REQUEST : [];

        // Check request
        if (empty($request)) {
            return;
        }

        // Check if we have some terms
        if (empty($this->terms)) {
            return;
        }

        // Check integrity
        if (!isset($request['taxonomy'])) {
            return;
        }

        // Get current saved taxonomy
        $slug = $request['taxonomy'];

        // Check if tax exists or if its contents are empty
        if (!isset($this->terms[$slug])) {
            return;
        }

        /**
         * Build term contents.
         *
         * @var string $slug
         * @param array $contents
         * @return array $contents
         */
        $contents = apply_filters('olh_termhook_'.$slug.'_contents', []);

        // Make it works!
        foreach ($contents as $ctn) {
            $value = isset($request[$ctn['id']]) ? $request[$ctn['id']] : '';
            $prefix = str_replace(['%TERM%', '%SLUG%'], [$term_id, $slug], '%TERM%-%SLUG%');

            // WP 4.4
            if (function_exists('update_term_meta')) {
                Option::updateTermMeta($term_id, $slug.'-'.$ctn['id'], $value);
            }
            else {
                Option::update($prefix.'-'.$ctn['id'], $value);
            }
        }

        return;
    }
}
