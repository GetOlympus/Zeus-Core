<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins;

/**
 * Clean The Events Calendar Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners\Plugins
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/the-events-calendar/
 *
 */

class TheEventsCalendarPlugin
{
    /**
     * @var array
     */
    protected $available = [
        'remove_meta_box',
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

            if ('remove_meta_box' === $key && OL_ZEUS_ISADMIN) {
                add_action('wp_dashboard_setup', function () {
                    remove_meta_box('tribe_dashboard_widget', 'dashboard', 'normal');
                });

                continue;
            }
        }
    }
}
