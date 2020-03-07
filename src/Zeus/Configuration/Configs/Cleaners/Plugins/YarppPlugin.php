<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins;

/**
 * Clean YARPP Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners\Plugins
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/yet-another-related-posts-plugin/
 *
 */

class YarppPlugin
{
    /**
     * @var array
     */
    protected $available = [
        'enqueue_styles',
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
        if (empty($settings) || !function_exists('yarpp_related')) {
            return;
        }

        $this->settings = $settings;

        // Initialize
        $this->init();
    }

    /**
     * Initialize main functions.
     */
    protected function init() : void
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

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_action('wp_print_styles', function () {
                    wp_dequeue_style('yarppWidgetCss');
                    wp_deregister_style('yarppRelatedCss');
                });
                add_action('wp_footer', function () {
                    wp_dequeue_style('yarppRelatedCss');
                });

                continue;
            }
        }
    }
}
