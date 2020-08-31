<?php

namespace GetOlympus\Zeus\Utils;

use GetOlympus\Zeus\Utils\Option;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Return $_REQUEST parameters when it is needed.
 *
 * @package    OlympusZeusCore
 * @subpackage Utils
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Request
{
    /**
     * Return a slug list where it is authorized to render assets.
     *
     * @return array
     */
    public static function authorizedAssets() : array
    {
        return [
            'admin', 'comment', 'post', 'user', 'widget'
        ];
    }

    /**
     * Return $_GET value.
     *
     * @param  string  $param
     * @param  string  $default
     *
     * @return string
     */
    public static function get($param, $default = '') : string
    {
        return SymfonyRequest::createFromGlobals()->query->get($param, $default);
    }

    /**
     * Get used slug in current admin panel page.
     *
     * @return string
     */
    public static function getCurrentSlug() : string
    {
        // Defintions
        $slug = self::get('post_type', '');

        // Define current post type's contents
        if (empty($slug)) {
            // Get slug by post type
            $post = self::get('post', 0);

            if (!empty($post)) {
                return get_post_type($post);
            }

            global $pagenow;

            // Custom page slug
            if (in_array($pagenow, ['admin.php'])) {
                return 'admin';
            }

            // Post slug
            if (in_array($pagenow, ['edit.php', 'post-new.php', 'post.php', 'edit-tags.php'])) {
                return 'post';
            }

            // Comment slug
            if (in_array($pagenow, ['edit-comments.php', 'comment.php'])) {
                return 'comment';
            }

            // Media slug
            if (in_array($pagenow, ['upload.php', 'media-new.php'])) {
                return 'attachment';
            }

            // User slug
            if (in_array($pagenow, ['users.php', 'user-new.php', 'user-edit.php', 'profile.php'])) {
                return 'user';
            }

            // Appearance slug
            if ('nav-menus.php' == $pagenow) {
                return 'nav_menu_item';
            }
        }

        return $slug;
    }

    /**
     * Return $_POST value.
     *
     * @param  string  $param
     * @param  string  $default
     */
    public static function post($param, $default = '')
    {
        return SymfonyRequest::createFromGlobals()->request->get($param, $default);
    }

    /**
     * Save request.
     *
     * @param  string  $identifier
     * @param  array   $ids
     *
     * @return bool
     */
    public static function save($identifier, $ids) : bool
    {
        // Works on $_POST
        $request = $_POST;

        if (empty($request) || empty($ids)) {
            return false;
        }

        $values = Option::getAdminOption($identifier);

        // Iterate
        foreach ($request as $k => $v) {
            // Don't register this default value
            if (in_array($k, ['updated', 'submit']) || !in_array($k, $ids)) {
                continue;
            }

            // Register settings
            $values[$k] = $v;
        }


        Option::set($identifier, $values);

        return true;
    }

    /**
     * Save request.
     *
     * @param  string  $identifier
     * @param  array   $ids
     *
     * @return bool
     */
    public static function upload($identifier, $ids) : bool
    {
        // Work on $_FILES
        $files = $_FILES;

        if (empty($files) || empty($ids)) {
            return false;
        }

        // Get required files
        include_once ABSPATH.'wp-admin'.S.'includes'.S.'image.php';
        include_once ABSPATH.'wp-admin'.S.'includes'.S.'file.php';
        include_once ABSPATH.'wp-admin'.S.'includes'.S.'media.php';

        $values = Option::getAdminOption($identifier);

        // Iterate
        foreach ($files as $k => $v) {
            // Do nothing if no file is defined
            if (empty($v['tmp_name']) || !in_array($k, $ids)) {
                continue;
            }

            $file = wp_handle_upload($v, ['test_form' => false]);

            // Register settings
            $values[$k] = $file['url'];
        }

        Option::set($identifier, $values);

        return true;
    }
}
