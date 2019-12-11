<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Clean Yoast SEO Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/wordpress-seo/
 *
 */

class HelpersPluginYoast
{
    /**
     * @var array
     */
    protected $available = [
        'remove_breadcrumbs_duplicates', 'remove_comment', 'remove_meta_box',
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor.
     *
     * @param  array   $settings
     */
    public function __construct($settings)
    {
        if (empty($settings) || !defined('WPSEO_VERSION')) {
            return;
        }

        $this->settings = $settings;

        // Initialize
        $this->init();
    }

    /**
     * Initialize main functions.
     */
    public function init()
    {
        // Special case
        if (is_bool($this->settings) && $this->settings) {
            $this->settings = $this->available;
        }

        // Iterate
        foreach ($this->settings as $key) {
            $key = strtolower($key);

            if (!in_array($key, $this->available)) {
                continue;
            }

            if ('remove_breadcrumbs_duplicates' === $key) {
                add_filter('wpseo_breadcrumb_single_link', function ($link) {
                    return false !== strpos($link, 'breadcrumb_last') ? '' : $link;
                });

                continue;
            }

            if ('remove_comment' === $key && !OL_ZEUS_ISADMIN) {
                add_action('get_header', function () {
                    ob_start(function ($html) {
                        return preg_replace('/^\n?\<\!\-\-.*?[Y]oast.*?\-\-\>\n?$/mi', '', $html);
                    });
                });

                add_action('wp_head', function () {
                    ob_end_flush();
                }, 999);

                continue;
            }

            if ('remove_meta_box' === $key && OL_ZEUS_ISADMIN) {
                add_action('wp_dashboard_setup', function () {
                    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
                });

                continue;
            }
        }
    }
}
