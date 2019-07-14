<?php

namespace GetOlympus\Zeus\Posttype\Interface;

/**
 * Post type hook interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface PosttypeHookInterface
{
    /**
     * Hook to change columns on post type list page.
     *
     * @param  array   $columns
     * @return array   $columns
     */
    public function manageEditColumns($columns);

    /**
     * Hook to add featured image to column.
     *
     * @param  string  $column
     * @param  integer $post_id
     */
    public function managePostsCustomColumn($column, $post_id);

    /**
     * Hook building custom permalinks for post types.
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
     *
     * @param  string  $post_link
     * @param  object  $post
     * @param  boolean $leavename
     * @param  boolean $sample
     * @return string  $permalink
     */
    public function postTypeLink($post_link, $post, $leavename, $sample);

    /**
     * Hook building custom metaboxes for Post types.
     */
    public function postTypeMetaboxesDisplay();

    /**
     * Hook saving custom fields for Post types.
     */
    public function postTypeSave();

    /**
     * Hook building custom options in Permalink settings page.
     */
    public function postTypeSettings();

    /**
     * Hook to display input value on Permalink settings page.
     *
     * @param  array   $vars
     */
    public function postTypeSettingFunc($vars);

    /**
     * Hook to display hidden input on Permalink settings title page.
     */
    public function postTypeSettingTitle();
}
