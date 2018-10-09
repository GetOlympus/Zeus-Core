<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Helpers\Controller\HelpersClean;

/**
 * Clean Core helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

class HelpersCleanCore extends HelpersClean
{
    /**
     * @var array
     */
    protected $available = [
        'autosave',           // Remove auto-save posts
        'heartbeat-admin',    // Remove HeartBeat scripts in all admin panel pages
        'heartbeat-all',      // Remove HeartBeat scripts in all WordPress pages
        'heartbeat-dashboard',// Remove HeartBeat scripts in admin panel dashboard only
        'jquery-migrate',     // Remove jQuery Migrate default script
        'json-api',           // Remove json api and link from header
        'post-custom-metabox',// Remove post custom metaboxes from post editor to prevent very slow queries
        'shutdown',           // Define wether if WP has to shut the DB connections off or not
        'xmlrpc',             // Remove XMLRPC
    ];

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param array $args
     */
    public function init($args)
    {
        if (empty($args)) {
            return;
        }

        // Special case
        if (is_bool($args) && $args) {
            $args = $this->available;
        }

        // Iterate on all
        foreach ($args as $key) {
            $key = strtolower($key);

            if (!in_array($key, $this->available)) {
                continue;
            }

            $key = str_replace('_', '-', $key);

            $function = Helpers::toFunctionFormat($key);
            $function = 'core'.ucfirst($function);
            $this->$function();
        }
    }

    /**
     * Remove auto-save posts
     */
    public function coreAutosave()
    {
        if (OL_ZEUS_ISADMIN) {
            return;
        }

        add_action('wp_print_scripts', function () {
            wp_deregister_script('autosave');
        });
    }

    /**
     * Remove HeartBeat scripts
     */
    public function coreHeartbeat($location)
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
    public function coreHeartbeatAdmin()
    {
        HelpersCleanCore::coreHeartbeat('admin');
    }

    /**
     * Remove HeartBeat scripts in all WordPress pages
     */
    public function coreHeartbeatAll()
    {
        HelpersCleanCore::coreHeartbeat('all');
    }

    /**
     * Remove HeartBeat scripts in admin panel dashboard only
     */
    public function coreHeartbeatDashboard()
    {
        HelpersCleanCore::coreHeartbeat('dashboard');
    }

    /**
     * Remove jQuery Migrate default script
     */
    public function coreJqueryMigrate()
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
    public function coreJsonApi()
    {
        if (!OL_ZEUS_ISADMIN) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }

        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);

        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');

        // add_filter('rest_enabled', '__return_false'); // Deprecated
        add_filter('rest_jsonp_enabled', '__return_false');
        add_filter('rest_authentication_errors', function ($access) {
            return new WP_Error(
                'rest_disabled',
                __('The REST API on this site has been disabled.'),
                [
                    'status' => rest_authorization_required_code(),
                ]
            );
        });
    }

    /**
     * Remove post custom metaboxes from post editor to prevent very slow queries
     */
    public function corePostCustomMetabox()
    {
        $pts = get_post_types('', 'names');

        foreach ($pts as $post_type) {
            remove_meta_box('postcustom', $post_type, 'normal');
        }
    }

    /**
     * Define wether if WP has to shut the DB connections off or not
     */
    public function coreShutdown()
    {
        add_action('shutdown', function () {
            global $wpdb;
            unset($wpdb);
        }, 99);
    }

    /**
     * Remove XMLRPC
     */
    public function coreXmlrpc()
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
