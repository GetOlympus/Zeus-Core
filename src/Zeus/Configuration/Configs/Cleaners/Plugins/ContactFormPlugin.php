<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners\Plugins;

/**
 * Clean Contact Form 7 Plugin helper
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners\Plugins
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.9
 * @see        https://wordpress.org/plugins/contact-form-7/
 *
 */

class ContactFormPlugin
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

            if ('enqueue_styles' === $key && !OL_ZEUS_ISADMIN) {
                add_action('wp_enqueue_scripts', function () {
                    wp_localize_script('contact-form-7', 'wpcf7', [
                        'apiSettings' => [
                            'root'      => esc_url_raw(rest_url('contact-form-7/v1')),
                            'namespace' => 'contact-form-7/v1',
                        ],
                        'jqueryUi'    => 1,
                    ]);
                }, 10);
                continue;
            }
        }
    }
}
