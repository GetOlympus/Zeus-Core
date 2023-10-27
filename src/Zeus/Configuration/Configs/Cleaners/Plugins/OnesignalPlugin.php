<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins;

/**
 * Clean Onesignal Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners\Plugins
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/onesignal-free-web-push-notifications/
 *
 */

class OnesignalPlugin
{
    /**
     * @var array
     */
    protected $available = [
        'onesignal_send_notification',
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

            if ('onesignal_send_notification' === $key) {
                add_filter('onesignal_send_notification', function ($fields, $new_status, $old_status, $post) {
                    // Store old data
                    $sitename = $fields['headings']['en'];
                    $contents = $fields['contents']['en'];

                    // Update fields
                    $fields['headings']['en'] = $contents;
                    $fields['contents']['en'] = empty($post->post_excerpt) ? $sitename : $post->post_excerpt;

                    // Return fields
                    return $fields;
                }, 10, 4);

                continue;
            }
        }
    }
}
