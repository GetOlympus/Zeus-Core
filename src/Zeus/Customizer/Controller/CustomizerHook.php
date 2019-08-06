<?php

namespace GetOlympus\Zeus\Customizer\Controller;

use GetOlympus\Zeus\Customizer\Implementation\CustomizerHookImplementation;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Works with Customizer Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

class CustomizerHook implements CustomizerHookImplementation
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var Customizer
     */
    protected $customizer;

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * Constructor.
     *
     * @param  Customizer $customizer
     */
    public function __construct($customizer)
    {
        $this->customizer = $customizer;

        // Get panels
        $panels = $this->customizer->getModel()->getPanels();

        // Check panels
        if (!empty($panels)) {
            $homeurl = get_home_url();

            foreach ($panels as $key => $options) {
                if (!isset($options['_redirect']) || empty($options['_redirect'])) {
                    continue;
                }

                // Update pages
                $this->pages[] = [
                    'identifier' => $key,
                    'path' => $homeurl,
                ];
            }

            // Update args
            $this->args = [
                'login_url'        => wp_login_url(),
                'lostpassword_url' => wp_lostpassword_url(),
                'register_url'     => wp_registration_url(),
                'site_url'         => get_option('siteurl'),
                'pages'            => $this->pages,
            ];
        }

        // Customize and manipulate the Theme Customization admin screen
        add_action('customize_register', [&$this, 'customizeRegister'], 10, 1);

        // Add page redirect if necessary
        add_filter('template_include', [&$this, 'customizeTemplateRedirect'], 99);
    }

    /**
     * Customize and manipulate the Theme Customization admin screen.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
     *
     * @param  object  $wp_customize
     */
    public function customizeRegister($wp_customize)
    {
        $controls = $this->customizer->getModel()->getControls();

        // Check controls
        if (empty($controls)) {
            throw new CustomizerException(Translate::t('customizer.errors.no_controls_to_display'));
        }

        // Displays everything needed
        $this->displayPanels($wp_customize);
        $this->displaySections($wp_customize);
        $this->displayControls($wp_customize, $controls);

        // Load customizer assets
        add_action('customize_controls_enqueue_scripts', [&$this, 'scriptsEnqueue'], 25);
        add_action('customize_preview_init', [&$this, 'scriptsPreview'], 25);
    }

    /**
     * Add page redirect if necessary.
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/template_include
     *
     * @param  string  $template
     */
    public function customizeTemplateRedirect($template)
    {
        $panels = $this->customizer->getModel()->getPanels();

        if (!is_customize_preview() || !is_user_logged_in() || empty($panels)) {
            return $template;
        }

        $request = Request::get('panel-redirect');
        $default_templates = $this->customizer->getDefaultTemplates();

        // Iterate on panels to find page redirect
        foreach ($panels as $id => $options) {
            if (!isset($options['_redirect']) || empty($options['_redirect'])) {
                continue;
            }

            // Check request panel
            if ($id !== $request) {
                continue;
            }

            return $options['_redirect'];
        }

        return $template;
    }

    /**
     * Displays controls.
     *
     * @param  object  $wp_customize
     * @param  array   $controls
     */
    public function displayControls($wp_customize, $controls)
    {
        // Get available control types
        $available_types = $this->customizer->getAvailableTypes();
        $special_types = $this->customizer->getAvailableTypes('special');

        // Iterate on all controls
        foreach ($controls as $id => $options) {
            $type = $options['type'];

            if (!in_array($type, $available_types)) {
                continue;
            }

            $_settings = $options['_settings'];
            unset($options['_settings']);

            // Set options
            $options = $this->getControlOptions($options, $type);
            $identifier = isset($options['settings']) ? $options['settings'] : $id;

            // Add _settings and remove them from options array
            $wp_customize->add_setting($identifier, $_settings);

            // Check control type & Add control
            if (!in_array($type, $special_types)) {
                $wp_customize->add_control($identifier, $options);
                continue;
            } else {
                $class = 'WP_Customize_'.ucwords(str_replace('-', '_', $type), '_').'_Control';
                $wp_customize->add_control(new $class($wp_customize, $identifier, $options));
            }
        }
    }

    /**
     * Displays panels.
     *
     * @param  object  $wp_customize
     */
    public function displayPanels($wp_customize)
    {
        $panels = $this->customizer->getModel()->getPanels();

        // Check panels
        if (empty($panels)) {
            return;
        }

        // Iterate on all panels
        foreach ($panels as $id => $options) {
            $wp_customize->add_panel($id, $options);
        }
    }

    /**
     * Displays sections.
     *
     * @param  object  $wp_customize
     */
    public function displaySections($wp_customize)
    {
        $sections = $this->customizer->getModel()->getSections();

        // Check sections
        if (empty($sections)) {
            return;
        }

        // Iterate on all sections
        foreach ($sections as $id => $options) {
            $wp_customize->add_section($id, $options);
        }
    }

    /**
     * Get control options.
     *
     * @param  array   $options
     * @param  string  $type
     *
     * @return mixed
     */
    public function getControlOptions($options, $type)
    {
        // Get available control types
        $special_types = $this->customizer->getAvailableTypes('special');

        // Get available mime types
        $mime_types = $this->customizer->getAvailableMimetypes();

        // Default values
        $opts = [
            'label'       => $options['label'],
            'description' => $options['description'],
            'section'     => $options['section'],
        ];

        // Check control type
        if (!in_array($options['type'], $special_types)) {
            // Check options
            $opts['choices'] = isset($options['choices']) ? $options['choices'] : [];
            $opts['input_attrs'] = isset($options['input_attrs']) ? $options['input_attrs'] : [];

            return $opts;
        }

        $opts['settings'] = $options['settings'];

        // Check special types
        if ('color' === $options['type']) {
            // WP_Customize_Color_Control
            // Nothing to do
        } else if ('media' === $options['type']) {
            // WP_Customize_Media_Control
            $opts['mime_type']     = isset($options['mime_type']) ? $options['mime_type'] : 'image';
            $opts['mime_type']     = !in_array($opts['mime_type'], $mime_types) ? 'image' : $opts['mime_type'];

            $opts['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
                'select'       => Translate::t('customizer.labels.control_media_select'),
                'change'       => Translate::t('customizer.labels.control_media_change'),
                'default'      => Translate::t('customizer.labels.control_media_default'),
                'remove'       => Translate::t('customizer.labels.control_media_remove'),
                'placeholder'  => Translate::t('customizer.labels.control_media_placeholder'),
                'frame_title'  => Translate::t('customizer.labels.control_media_frame_title'),
                'frame_button' => Translate::t('customizer.labels.control_media_frame_button'),
            ];
        } else if ('image' === $options['type']) {
            // WP_Customize_Image_Control
            $opts['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
                'select'       => Translate::t('customizer.labels.control_image_select'),
                'change'       => Translate::t('customizer.labels.control_image_change'),
                'default'      => Translate::t('customizer.labels.control_image_default'),
                'remove'       => Translate::t('customizer.labels.control_image_remove'),
                'placeholder'  => Translate::t('customizer.labels.control_image_placeholder'),
                'frame_title'  => Translate::t('customizer.labels.control_image_frame_title'),
                'frame_button' => Translate::t('customizer.labels.control_image_frame_button'),
            ];
        } else if ('cropped-image' === $options['type']) {
            // WP_Customize_Cropped_Image_Control
            $opts['flex_height']   = isset($options['flex_height']) ? $options['flex_height'] : false;
            $opts['flex_width']    = isset($options['flex_width']) ? $options['flex_width'] : false;
            $opts['height']        = isset($options['height']) ? $options['height'] : 150;
            $opts['width']         = isset($options['width']) ? $options['width'] : 150;

            $opts['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
                'select'       => Translate::t('customizer.labels.control_image_select'),
                'change'       => Translate::t('customizer.labels.control_image_change'),
                'default'      => Translate::t('customizer.labels.control_image_default'),
                'remove'       => Translate::t('customizer.labels.control_image_remove'),
                'placeholder'  => Translate::t('customizer.labels.control_image_placeholder'),
                'frame_title'  => Translate::t('customizer.labels.control_image_frame_title'),
                'frame_button' => Translate::t('customizer.labels.control_image_frame_button'),
            ];
        } else if ('date-time' === $options['type']) {
            // WP_Customize_Date_Time_Control
            $opts['allow_past_date'] = isset($options['allow_past_date']) ? $options['allow_past_date'] : true;
            $opts['include_time']    = isset($options['include_time']) ? $options['include_time'] : true;
            $opts['max_year']        = isset($options['max_year']) ? $options['max_year'] : '9999';
            $opts['min_year']        = isset($options['min_year']) ? $options['min_year'] : '1000';
            $opts['twelve_hour_format'] = isset($options['twelve_hour_format'])
                ? $options['twelve_hour_format']
                : false;
        }

        return $opts;
    }

    /**
     * Make script callable from public folder.
     *
     * @param  string  $filepath
     * @param  string  $folder
     *
     * @return string  $fileuri
     */
    public function getScript($filepath, $folder)
    {
        // Update details
        $basename = basename($filepath);
        $fileuri  = OL_ZEUS_URI.$folder.S.$basename;
        $source   = rtrim(dirname($filepath), S);
        $target   = rtrim(OL_ZEUS_DISTPATH, S).S.$folder;

        // Update script path on dist accessible folder
        Helpers::copyFile($source, $target, $basename);

        return $fileuri;
    }

    /**
     * Enqueue scripts.
     */
    public function scriptsEnqueue()
    {
        // Get Zeus Customizer script
        $script  = $this->customizer->getScripts()['customizer'];
        $fileuri = $this->getScript($script, 'js');

        // Enqueue scripts and set usefull urls
        wp_enqueue_script('zeus-customizer', esc_url($fileuri), [], false, true);
        wp_localize_script('zeus-customizer', 'ZeusSettings', $this->args);
    }

    /**
     * Preview styles.
     */
    public function scriptsPreview()
    {
        if (!is_customize_preview()) {
            return;
        }

        // Get Zeus Customizer preview script
        $script = $this->customizer->getScripts()['previewer'];
        $fileuri = $this->getScript($script, 'js');

        // Enqueue scripts and set usefull urls
        add_action('wp_footer', function () use ($fileuri) {
            wp_enqueue_script('zeus-customizer-preview', esc_url($fileuri), [], false, true);
            wp_localize_script('zeus-customizer-preview', 'ZeusSettings', $this->args);
        });
    }
}
