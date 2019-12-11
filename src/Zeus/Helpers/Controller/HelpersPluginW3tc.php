<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Clean W3 Total Cache Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/w3-total-cache/
 *
 */

class HelpersPluginW3tc
{
    /**
     * @var array
     */
    protected $available = [
        'remove_comment', 'remove_meta_box',
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
        if (empty($settings)) {
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

            if ('remove_comment' === $key) {
                add_filter('w3tc_can_print_comment', '__return_false', 10, 1);
                continue;
            }

            if ('remove_meta_box' === $key && OL_ZEUS_ISADMIN) {
                add_action('wp_dashboard_setup', function () {
                    remove_meta_box('w3tc_latest', 'dashboard', 'normal');
                });

                continue;
            }
        }
    }
}
