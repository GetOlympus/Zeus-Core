<?php

namespace GetOlympus\Hera\Term\Controller;

/**
 * Term hook interface.
 *
 * @package Olympus Hera
 * @subpackage Term\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface TermHookInterface
{
    /**
     * Hook building custom fields.
     *
     * @param string|object $term
     * @param string        $mode
     */
    public function addFields($term, $mode = 'edit');

    /**
     * Hook building custom fields on term homepage.
     *
     * @param string|object $term
     */
    public function addFormFields($term);

    /**
     * Hook building custom fields.
     *
     * @param string|object $term
     */
    public function editFormFields($term);

    /**
     * Hook to add custom column.
     *
     * @param string $content
     * @param string $column
     * @param int $term_id
     */
    public function manageCustomColumn($content, $column, $term_id);

    /**
     * Hook to change columns on term list page.
     *
     * @param array $columns
     * @return array $columns
     */
    public function manageEditColumns($columns);

    /**
     * Hook building custom fields for Post types.
     *
     * @param number $term_id
     * @return number|void
     */
    public function saveFields($term_id);
}
