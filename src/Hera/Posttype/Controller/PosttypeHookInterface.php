<?php

namespace GetOlympus\Hera\Posttype\Controller;

/**
 * Post type hook interface.
 *
 * @package Olympus Hera
 * @subpackage Posttype\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface PosttypeHookInterface
{
    /**
     * Hook to change columns on post type list page.
     *
     * @param array $columns
     * @return array $columns
     */
    public function manageEditColumns($columns);

    /**
     * Hook to add featured image to column.
     *
     * @param string $column
     * @param integer $post_id
     */
    public function managePostsCustomColumn($column, $post_id);

    /**
     * Hook building custom permalinks for post types.
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
     *
     * @param string $permalink
     * @param integer $post_id
     * @param boolean $leavename
     * @return string $permalink
     */
    public function postTypeLink($permalink, $post_id, $leavename);

    /**
     * Hook building custom fields for CPTS.
     */
    public function postTypeFieldDisplay();

    /**
     * Hook building custom fields for Post types.
     */
    public function postTypeSave();

    /**
     * Hook building custom options in Permalink settings page.
     */
    public function postTypeSettings();

    /**
     * Hook to display input value on Permalink settings page.
     *
     * @param array $vars
     */
    public function postTypeSettingFunc($vars);

    /**
     * Hook to display hidden input on Permalink settings title page.
     */
    public function postTypeSettingTitle();
}
