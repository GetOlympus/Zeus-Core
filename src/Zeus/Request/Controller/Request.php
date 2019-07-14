<?php

namespace GetOlympus\Zeus\Request\Controller;

use GetOlympus\Zeus\Request\Implementation\RequestImplementation;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Return $_REQUEST parameters when it is needed.
 *
 * @package    OlympusZeusCore
 * @subpackage Request\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Request implements RequestImplementation
{
    /**
     * Return a slug list where it is authorized to render assets.
     *
     * @return array   $authorizedPage
     */
    public static function authorizedAssets()
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
     * @return string  $value
     */
    public static function get($param, $default = '')
    {
        return SymfonyRequest::createFromGlobals()->query->get($param, $default);
    }

    /**
     * Get used slug in current admin panel page.
     *
     * @return string  $slug
     */
    public static function getCurrentSlug()
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
     * @return string  $value
     */
    public static function post($param, $default = '')
    {
        return SymfonyRequest::createFromGlobals()->request->get($param, $default);
    }
}
