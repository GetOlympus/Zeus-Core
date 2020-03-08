<?php

namespace GetOlympus\Zeus\Term;

use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Request;
use GetOlympus\Zeus\WalkerSingle\Controller\WalkerSingle;

/**
 * Works with Term Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage Term
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class TermHook
{
    /**
     * @var Term
     */
    protected $term;

    /**
     * Constructor.
     *
     * @param  Term    $term
     */
    public function __construct($term)
    {
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

        $slug = $term->getModel()->getSlug();

        // Check slug
        if (empty($slug)) {
            return;
        }

        $this->term = $term;

        $args = $this->term->getModel()->getArgs();
        $is_single = isset($args['choice']) && 'single' === $args['choice'] ? true : false;

        // Edit custom fields
        add_action($slug.'_edit_form_fields', [$this, 'editFormFields'], 10, 1);

        // Add custom fields
        if (!empty($this->term->getModel()->getFields())) {
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
            add_filter('wp_terms_checklist_args', function ($args, $post_id) use ($slug) {
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
     * @param  mixed   $term
     * @param  string  $mode
     */
    protected function addFields($term, $mode = 'edit') : void
    {
        // Check mode
        $mode = in_array($mode, ['add', 'edit']) ? $mode : 'edit';
        $fields = $this->term->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
        }

        // Get current
        $term = is_object($term) ? $term : get_term($term);
        $vars = [];

        // Prepare admin scripts and styles
        $assets = [
            'scripts' => [],
            'styles'  => [],
        ];

        // Get fields
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            // Update scripts and styles
            $fieldassets = $field->assets();

            if (!empty($fieldassets)) {
                $assets['scripts'] = array_merge($assets['scripts'], $fieldassets['scripts']);
                $assets['styles']  = array_merge($assets['styles'], $fieldassets['styles']);
            }

            $vars['fields'][] = $field->prepare('term-'.$mode, $term, 'term');
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'term.html.twig', $vars, $assets);
        $render->view();
    }

    /**
     * Hook building custom fields on term homepage.
     *
     * @param  mixed   $term
     */
    public function addFormFields($term) : void
    {
        $this->addFields($term, 'add');
    }

    /**
     * Hook building custom fields.
     *
     * @param  mixed   $term
     */
    public function editFormFields($term) : void
    {
        $this->addFields($term, 'edit');
    }

    /**
     * Hook to add custom column.
     *
     * @param  string  $content
     * @param  string  $column
     * @param  int     $term_id
     */
    public function manageCustomColumn($content, $column, $term_id) : void
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
         * @param  string  $content
         * @param  string  $column
         * @param  int     $term_id
         */
        do_action('ol_zeus_termhook_manage_'.$current.'_custom_column', $content, $column, $term_id);
    }

    /**
     * Hook to change columns on term list page.
     *
     * @param  array   $columns
     *
     * @return array
     */
    public function manageEditColumns($columns) : array
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
         * @var    string  $current
         * @param  array   $columns
         *
         * @return array
         */
        return apply_filters('ol_zeus_termhook_manage_edit-'.$current.'_columns', $columns);
    }

    /**
     * Hook building custom fields for Post types.
     *
     * @param  int     $term_id
     *
     * @return bool
     */
    public function saveFields($term_id) : bool
    {
        // No term or no fields
        if (!isset($term_id) || empty($term_id) || empty($this->term->getModel()->getFields())) {
            return false;
        }

        // Check slug
        $slug         = $this->term->getModel()->getSlug();
        $request_slug = Request::post('taxonomy', '');

        // Check integrity
        if (empty($request_slug) || $request_slug !== $slug) {
            return false;
        }

        // Remove action hook and add it again later for no infinite loop
        remove_action('created_'.$slug, [$this, 'saveFields']);
        remove_action('edited_'.$slug, [$this, 'saveFields']);

        $fields = $this->term->getModel()->getFields();

        /**
         * Fires for all term's fields.
         *
         * @var    string  $slug
         * @param  int     $term_id
         * @param  array   $fields
         */
        do_action('ol_zeus_termhook_save_'.$slug, $term_id, $fields);

        // Update all metas
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
            $option_name = $slug.'-'.$id;

            // Check value
            if (is_null($value)) {
                $value = Option::getTermMeta($term_id, $option_name);
            }

            /**
             * Filter the value content.
             *
             * @var    string  $slug
             * @param  object  $value
             * @param  int     $term_id
             * @param  string  $option_name
             *
             * @return object
             */
            $value = apply_filters('ol_zeus_termhook_save_'.$slug.'_field', $value, $term_id, $option_name);

            // Updates meta
            Option::updateTermMeta($term_id, $option_name, $value);
        }

        // Add back action hook again for no infinite loop
        add_action('created_'.$slug, [$this, 'saveFields'], 10, 2);
        add_action('edited_'.$slug, [$this, 'saveFields'], 10, 2);

        return true;
    }
}
