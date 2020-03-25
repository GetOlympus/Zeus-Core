<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners;

use GetOlympus\Zeus\Configuration\Configs\Cleaners\Cleaner;

/**
 * Core cleaner
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class Core extends Cleaner
{
    /**
     * @var array
     */
    protected $available = [
        'autosave'            => true,  // Remove auto-save posts
        'file-edit'           => true,  // Disallow file edition from admin panel
        'heartbeat-admin'     => true,  // Remove HeartBeat scripts in all admin panel pages
        'heartbeat-all'       => true,  // Remove HeartBeat scripts in all WordPress pages
        'heartbeat-dashboard' => true,  // Remove HeartBeat scripts in admin panel dashboard only
        'jquery-migrate'      => true,  // Remove jQuery Migrate default script
        'json-api'            => true,  // Remove json api and link from header
        'post-custom-metabox' => true,  // Remove post custom metaboxes from post editor to prevent very slow queries
        'rest-api'            => false, // Disable REST api
        'shutdown'            => true,  // Define wether if WP has to shut the DB connections off or not
        'xmlrpc'              => true,  // Remove XMLRPC
    ];

    /**
     * Remove auto-save posts
     */
    protected function coreAutosave() : void
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        add_action('wp_print_scripts', function () {
            wp_deregister_script('autosave');
        });
    }

    /**
     * Disallow file edition from admin panel
     */
    protected function coreFileEdit() : void
    {
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
    }

    /**
     * Remove HeartBeat scripts
     */
    protected function coreHeartbeat($location) : void
    {
        add_action('init', function () use ($location) {
            if ('all' === $location) {
                wp_deregister_script('heartbeat');
            } else if (OL_ZEUS_ISADMIN) {
                global $pagenow;

                if (('admin' === $location && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow) ||
                    ('dashboard' === $location && 'index.php' === $pagenow)
                ) {
                    wp_deregister_script('heartbeat');
                }
            }
        }, 1);
    }

    /**
     * Remove HeartBeat scripts in all admin panel pages
     */
    protected function coreHeartbeatAdmin() : void
    {
        $this->coreHeartbeat('admin');
    }

    /**
     * Remove HeartBeat scripts in all WordPress pages
     */
    protected function coreHeartbeatAll() : void
    {
        $this->coreHeartbeat('all');
    }

    /**
     * Remove HeartBeat scripts in admin panel dashboard only
     */
    protected function coreHeartbeatDashboard() : void
    {
        $this->coreHeartbeat('dashboard');
    }

    /**
     * Remove jQuery Migrate default script
     */
    protected function coreJqueryMigrate() : void
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        add_action('wp_default_scripts', function ($scripts) {
            if (!empty($scripts->registered['jquery'])) {
                $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
            }
        });
    }

    /**
     * Remove json api and link from header
     */
    protected function coreJsonApi() : void
    {
        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');

        add_filter('rest_enabled', '__return_false'); // Deprecated
        add_filter('rest_jsonp_enabled', '__return_false');
    }

    /**
     * Remove post custom metaboxes from post editor to prevent very slow queries
     * @see  https://9seeds.com/wordpress-admin-post-editor-performance/
     */
    protected function corePostCustomMetabox() : void
    {
        add_action('wp_dashboard_setup', function () {
            $pts = get_post_types('', 'names');

            foreach ($pts as $post_type) {
                remove_meta_box('postcustom', $post_type, 'normal');
            }
        });
    }

    /**
     * Disable REST api
     */
    protected function coreRestApi() : void
    {
        if (!OL_ZEUS_ISADMIN) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }

        add_filter('rest_authentication_errors', function () {
            return new \WP_Error(
                'rest_disabled',
                __('The REST API on this site has been disabled.'),
                [
                    'status' => rest_authorization_required_code(),
                ]
            );
        });
    }

    /**
     * Define wether if WP has to shut the DB connections off or not
     */
    protected function coreShutdown() : void
    {
        add_action('shutdown', function () {
            global $wpdb;
            unset($wpdb);
        }, 99);
    }

    /**
     * Remove XMLRPC
     */
    protected function coreXmlrpc() : void
    {
        add_filter('xmlrpc_enabled', '__return_false');

        add_filter('wp_headers', function ($headers) {
            unset($headers['X-Pingback']);
            return $headers;
        });

        remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
        add_filter('xmlrpc_enabled', '__return_false');

        add_filter('xmlrpc_methods', function ($methods) {
            unset($methods['pingback.ping']);
            return $methods;
        });
    }
}
