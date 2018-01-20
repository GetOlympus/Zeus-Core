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
     * @var array
     */
    protected $fields;

    /**
     * @var boolean
     */
    protected $is_single;

    /**
     * @var string
     */
    protected $posttype;

    /**
     * @var string
     */
    protected $slug;

    /**
     * Constructor.
     *
     * @param string $slug
     * @param boolean $addCustomFields
     * @param boolean $isSingle
     */
    public function __construct($slug, $posttype, $fields, $is_single = false)
    {
        // Check slug or Admin panel
        if (empty($slug) || !OLH_ISADMIN) {
            return;
        }

        $this->slug = $slug;
        $this->fields = $fields;
        $this->posttype = $posttype;
        $this->is_single = $is_single;

        // Render assets
        Render::assets(['edit-tags.php', 'term.php'], $this->fields);

        // Edit custom fields
        add_action($slug.'_edit_form_fields', [$this, 'editFormFields'], 10, 1);

        // Add custom fields
        if (!empty($this->fields)) {
            add_action($slug.'_add_form_fields', [$this, 'addFormFields'], 10, 1);
        }

        // Save custom fields
        add_action('created_'.$slug, [$this, 'saveFields'], 10, 2);
        add_action('edited_'.$slug, [$this, 'saveFields'], 10, 2);

        // Display custom columns
        add_filter('manage_edit-'.$slug.'_columns', [$this, 'manageEditColumns'], 10);
        add_action('manage_'.$slug.'_custom_column', [$this, 'manageCustomColumn'], 11, 3);

        // Special case: single choice on post edit page
        if ($is_single) {
            // Apply filter
            add_filter('wp_terms_checklist_args', function ($args, $post_id) use ($slug){
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
     * @param string        $mode
     */
    public function addFields($term, $mode = 'edit')
    {
        // Check mode
        $mode = in_array($mode, ['add', 'edit']) ? $mode : 'edit';

        // Check fields
        if (empty($this->fields)) {
            return;
        }

        // Get current
        $term = is_object($term) ? $term : get_term($term);

        // Get fields
        foreach ($this->fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            $ctn = (array) $field->getModel()->getContents();
            $hasId = (boolean) $field->getModel()->getHasId();

            // Check fields
            if (empty($ctn)) {
                continue;
            }

            // Does the field have an ID
            if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                continue;
            }

            // Display field
            $field->render($ctn, [
                'structure' => '%TERM%-%SLUG%',
                'template'  => 'term-'.$mode,
                'term'      => $term,
            ]);
        }
    }

    /**
     * Hook building custom fields on term homepage.
     *
     * @param string|object $term
     */
    public function addFormFields($term)
    {
        $this->addFields($term, 'add');
    }

    /**
     * Hook building custom fields.
     *
     * @param string|object $term
     */
    public function editFormFields($term)
    {
        $this->addFields($term, 'edit');
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
        // No term or no fields
        if (!isset($term_id) || empty($term_id) || empty($this->fields)) {
            return;
        }

        // Check slug
        $slug = Request::post('taxonomy', '');

        // Check integrity
        if (empty($slug) || $slug !== $this->slug) {
            return;
        }

        // Remove action hook and add it again later for no infinite loop
        remove_action('created_'.$slug, [&$this, 'saveFields']);
        remove_action('edited_'.$slug, [&$this, 'saveFields']);

        /**
         * Fires for all term's fields.
         *
         * @var string $slug
         * @param int $term_id
         * @param array $fields
         */
        do_action('olh_termhook_save_'.$slug, $term_id, $this->fields);

        // Update all metas
        foreach ($this->fields as $field) {
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
                $value = Option::getTermMeta($term_id, $slug.'-'.$ctn['id']);
            }

            /**
             * Filter the value content.
             *
             * @var string $current
             * @param int $term_id
             * @param string $option_name
             * @param object $value
             * @return object $value
             */
            $value = apply_filters('olh_termhook_save_'.$slug.'_field', $value, $term_id, $slug.'-'.$ctn['id']);

            // Updates meta
            Option::updateTermMeta($term_id, $slug.'-'.$ctn['id'], $value);
        }

        // Add back action hook again for no infinite loop
        add_action('created_'.$slug, [$this, 'saveFields'], 10, 2);
        add_action('edited_'.$slug, [$this, 'saveFields'], 10, 2);

        return true;
    }
}
