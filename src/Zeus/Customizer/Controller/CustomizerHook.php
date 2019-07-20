<?php

namespace GetOlympus\Zeus\Customizer\Controller;

use GetOlympus\Zeus\Customizer\Implementation\CustomizerHookImplementation;
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
     * @var Customizer
     */
    protected $customizer;

    /**
     * Constructor.
     *
     * @param  Customizer $customizer
     */
    public function __construct($customizer)
    {
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

        $this->customizer = $customizer;

        // Customize and manipulate the Theme Customization admin screen
        add_action('customize_register', [&$this, 'customizeRegister']);
    }

    /**
     * Customize and manipulate the Theme Customization admin screen.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
     *
     * @param  object  $wp_customize
     */
    public function customizeRegister($wp_customize)
    {
        // Get vars
        $panels = $this->customizer->getModel()->getPanels();
        $sections = $this->customizer->getModel()->getSections();
        $controls = $this->customizer->getModel()->getControls();

        // Check controls
        if (empty($controls)) {
            throw new CustomizerException(Translate::t('customizer.errors.no_controls_to_display'));
        }

        // Displays everything needed
        $this->displayPanels($wp_customize, $panels);
        $this->displaySections($wp_customize, $sections);
        $this->displayControls($wp_customize, $controls);
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
        $choice_types = $this->customizer->getAvailableTypes('choice');
        $text_types = $this->customizer->getAvailableTypes('text');

        // Get available mime types
        $mime_types = $this->customizer->getAvailableMimetypes();

        // Iterate on all controls
        foreach ($controls as $id => $options) {
            if (!in_array($options['type'], $available_types)) {
                continue;
            }

            // Add setting
            $wp_customize->add_setting($id, $options['_settings']);

            // Check control type
            if (in_array($options['type'], $special_types)) {
                $type = ucfirst(str_replace('-', '_', $options['type']));
                $class = 'WP_Customize_'.$type.'_Control';

                if ('media' === $options['type']) {
                    $options = array_merge([
                        'mime_type'     => 'image',
                        'button_labels' => [
                            'select'       => Translate::t('customizer.labels.control_media_select'),
                            'change'       => Translate::t('customizer.labels.control_media_change'),
                            'default'      => Translate::t('customizer.labels.control_media_default'),
                            'remove'       => Translate::t('customizer.labels.control_media_remove'),
                            'placeholder'  => Translate::t('customizer.labels.control_media_placeholder'),
                            'frame_title'  => Translate::t('customizer.labels.control_media_frame_title'),
                            'frame_button' => Translate::t('customizer.labels.control_media_frame_button'),
                        ],
                    ], $options);

                    // Check mime type
                    $options['mime_type'] = !in_array($options['mime_type'], $mime_types)
                        ? 'image'
                        : $options['mime_type'];
                } else if ('image' === $options['type']) {
                    $options = array_merge([
                        'button_labels' => [
                            'select'       => Translate::t('customizer.labels.control_image_select'),
                            'change'       => Translate::t('customizer.labels.control_image_change'),
                            'default'      => Translate::t('customizer.labels.control_image_default'),
                            'remove'       => Translate::t('customizer.labels.control_image_remove'),
                            'placeholder'  => Translate::t('customizer.labels.control_image_placeholder'),
                            'frame_title'  => Translate::t('customizer.labels.control_image_frame_title'),
                            'frame_button' => Translate::t('customizer.labels.control_image_frame_button'),
                        ],
                    ], $options);
                } else if ('cropped-image' === $options['type']) {
                    $options = array_merge([
                        'flex_height'   => false,
                        'flex_width'    => false,
                        'height'        => 150,
                        'width'         => 150,
                        'button_labels' => [
                            'select'       => Translate::t('customizer.labels.control_image_select'),
                            'change'       => Translate::t('customizer.labels.control_image_change'),
                            'default'      => Translate::t('customizer.labels.control_image_default'),
                            'remove'       => Translate::t('customizer.labels.control_image_remove'),
                            'placeholder'  => Translate::t('customizer.labels.control_image_placeholder'),
                            'frame_title'  => Translate::t('customizer.labels.control_image_frame_title'),
                            'frame_button' => Translate::t('customizer.labels.control_image_frame_button'),
                        ],
                    ], $options);
                } else if ('date-time' === $options['type']) {
                    $options = array_merge([
                        'allow_past_date'    => true,
                        'include_time'       => true,
                        'max_year'           => '9999',
                        'min_year'           => '1000',
                        'twelve_hour_format' => true,
                    ], $options);
                }

                // Add control
                $wp_customize->add_control(new $class($wp_customize, $id, $options));

                continue;
            }

            // Check options
            $options = array_merge([
                'choices'     => [],
                'input_attrs' => [],
            ], $options);

            // Add control
            $wp_customize->add_control($id, $options);
        }
    }

    /**
     * Displays panels.
     *
     * @param  object  $wp_customize
     * @param  array   $panels
     */
    public function displayPanels($wp_customize, $panels)
    {
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
     * @param  array   $sections
     */
    public function displaySections($wp_customize, $sections)
    {
        // Check sections
        if (empty($sections)) {
            return;
        }

        // Iterate on all sections
        foreach ($sections as $id => $options) {
            $wp_customize->add_section($id, $options);
        }
    }
}
