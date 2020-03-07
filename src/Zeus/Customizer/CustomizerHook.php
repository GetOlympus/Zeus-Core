<?php

namespace GetOlympus\Zeus\Customizer;

use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Request;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Works with Customizer Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

class CustomizerHook
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
        add_action('customize_register', [$this, 'customizeRegister'], 10, 1);

        // Add page redirect if necessary
        add_filter('template_include', [$this, 'customizeTemplateRedirect'], 99);
    }

    /**
     * Customize and manipulate the Theme Customization admin screen.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
     *
     * @param  object  $wp_customize
     *
     * @throws CustomizerException
     */
    public function customizeRegister($wp_customize) : void
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
        add_action('customize_controls_enqueue_scripts', [$this, 'scriptsEnqueue'], 25);
        add_action('customize_preview_init', [$this, 'scriptsPreview'], 25);
    }

    /**
     * Add page redirect if necessary.
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/template_include
     *
     * @param  string  $template
     *
     * @return string
     */
    public function customizeTemplateRedirect($template) : string
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
    protected function displayControls($wp_customize, $controls) : void
    {
        // Get available control types
        $available_types = $this->customizer->getAvailableTypes();
        $special_types = $this->customizer->getAvailableTypes('special');

        // Iterate on all controls
        foreach ($controls as $id => $options) {
            $_settings = $options['_settings'];
            unset($options['_settings']);

            // Set options
            $options = $this->getControlOptions($options);
            $identifier = isset($options['settings']) ? $options['settings'] : $id;

            // Add _settings and remove them from options array
            $wp_customize->add_setting($identifier, $_settings);

            // Check control type & Add control
            if (in_array($options['type'], $available_types) && !in_array($options['type'], $special_types)) {
                $wp_customize->add_control($identifier, $options);
            } else {
                $options['type'] = str_replace('-', '_', $options['type']);

                // Uppercase first letter of each word
                $type = preg_replace_callback('/_([a-z]?)/', function ($m) {
                    return '_'.strtoupper($m[1]);
                }, $options['type']);

                // Uppercase first letter & add '_Control' for WP Controls
                $type = ucwords($type);
                $type .= '_Control' === substr($type, -8) ? '' : '_Control';

                $class_custom = '\\GetOlympus\\Control\\'.str_replace('_', '', $type);
                $class_wp     = '\\WP_Customize_'.$type;
                $class_other  = '\\'.$type;

                $class = class_exists($class_custom)
                    ? $class_custom : (class_exists($class_wp)
                        ? $class_wp : (class_exists($class_other)
                            ? $class_other : false
                        )
                    );

                if (!$class) {
                    continue;
                }

                $wp_customize->add_control(new $class($wp_customize, $identifier, $options));
            }
        }
    }

    /**
     * Displays panels.
     *
     * @param  object  $wp_customize
     */
    protected function displayPanels($wp_customize) : void
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
    protected function displaySections($wp_customize) : void
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
     *
     * @return array
     */
    protected function getControlOptions($options) : array
    {
        // Get available control & mime types
        $special_types = $this->customizer->getAvailableTypes('special');
        $mime_types = $this->customizer->getAvailableMimetypes();

        // Check control type
        if (!in_array($options['type'], $special_types)) {
            // Check options
            $options['choices']     = isset($options['choices']) ? $options['choices'] : [];
            $options['input_attrs'] = isset($options['input_attrs']) ? $options['input_attrs'] : [];

            return $options;
        }

        // Check special types
        if ('color' === $options['type']) {
            // WP_Customize_Color_Control
            // Nothing to do
        } else if ('media' === $options['type']) {
            // WP_Customize_Media_Control
            $options['mime_type'] = isset($options['mime_type']) ? $options['mime_type'] : 'image';
            $options['mime_type'] = !in_array($options['mime_type'], $mime_types) ? 'image' : $options['mime_type'];

            $options['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
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
            $options['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
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
            $options['flex_height'] = isset($options['flex_height']) ? $options['flex_height'] : false;
            $options['flex_width']  = isset($options['flex_width']) ? $options['flex_width'] : false;
            $options['height']      = isset($options['height']) ? $options['height'] : 150;
            $options['width']       = isset($options['width']) ? $options['width'] : 150;

            $options['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
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
            $options['allow_past_date'] = isset($options['allow_past_date']) ? $options['allow_past_date'] : true;
            $options['include_time']    = isset($options['include_time']) ? $options['include_time'] : true;
            $options['max_year']        = isset($options['max_year']) ? $options['max_year'] : '9999';
            $options['min_year']        = isset($options['min_year']) ? $options['min_year'] : '1000';
            $options['twelve_hour_format'] = isset($options['twelve_hour_format'])
                ? $options['twelve_hour_format']
                : false;
        }

        return $options;
    }

    /**
     * Make script callable from public folder.
     *
     * @param  string  $filepath
     * @param  string  $folder
     *
     * @return string
     */
    protected function getScript($filepath, $folder) : string
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
    public function scriptsEnqueue() : void
    {
        $fileuri = [];

        // Get Zeus Customizer script
        $adscript = $this->customizer->getAdminscripts()['customizer'];
        $fileuri['zeus'] = $this->getScript($adscript, 'js');

        // Get custom Customizer script
        $cuscript = $this->customizer->getScripts();

        if (isset($cuscript['customizer'])) {
            $fileuri['custom'] = $this->getScript($cuscript['customizer'], 'js');
        }

        // Enqueue scripts and set usefull urls
        foreach ($fileuri as $key => $script) {
            wp_enqueue_script($key.'-customizer', esc_url($script), [], false, true);
        }

        wp_localize_script('zeus-customizer', 'ZeusSettings', $this->args);
    }

    /**
     * Preview styles.
     */
    public function scriptsPreview() : void
    {
        if (!is_customize_preview()) {
            return;
        }

        $fileuri = [];

        // Get Zeus Customizer preview script
        $adscript = $this->customizer->getAdminscripts()['previewer'];
        $fileuri['zeus'] = $this->getScript($adscript, 'js');

        // Get custom Customizer preview script
        $cuscript = $this->customizer->getScripts();

        if (isset($cuscript['previewer'])) {
            $fileuri['custom'] = $this->getScript($cuscript['previewer'], 'js');
        }

        // Enqueue scripts and set usefull urls
        add_action('wp_footer', function () use ($fileuri) {
            foreach ($fileuri as $key => $script) {
                wp_enqueue_script($key.'-customizer-preview', esc_url($script), [], false, true);
            }

            wp_localize_script('zeus-customizer-preview', 'ZeusSettings', $this->args);
        });
    }
}
