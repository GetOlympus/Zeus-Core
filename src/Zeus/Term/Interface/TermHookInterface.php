<?php

namespace GetOlympus\Zeus\Term\Interface;

/**
 * Term hook interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Term\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface TermHookInterface
{
    /**
     * Hook building custom fields.
     *
     * @param  mixed   $term
     * @param  string  $mode
     */
    public function addFields($term, $mode = 'edit');

    /**
     * Hook building custom fields on term homepage.
     *
     * @param  mixed   $term
     */
    public function addFormFields($term);

    /**
     * Hook building custom fields.
     *
     * @param  mixed   $term
     */
    public function editFormFields($term);

    /**
     * Hook to add custom column.
     *
     * @param  string  $content
     * @param  string  $column
     * @param  integer $term_id
     */
    public function manageCustomColumn($content, $column, $term_id);

    /**
     * Hook to change columns on term list page.
     *
     * @param  array   $columns
     * @return array   $columns
     */
    public function manageEditColumns($columns);

    /**
     * Hook building custom fields for Post types.
     *
     * @param  integer $term_id
     * @return integer|void
     */
    public function saveFields($term_id);
}
