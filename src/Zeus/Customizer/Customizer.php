<?php

namespace GetOlympus\Zeus\Customizer;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Customizer\CustomizerHook;
use GetOlympus\Zeus\Customizer\CustomizerException;
use GetOlympus\Zeus\Customizer\CustomizerInterface;
use GetOlympus\Zeus\Customizer\CustomizerModel;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own customizer.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

abstract class Customizer extends Base implements CustomizerInterface
{
    /**
     * @var array
     */
    protected $available_types = [
        'default' => [
            'text', 'email', 'url', 'number', 'range', 'hidden', 'date',
            'textarea', 'checkbox', 'dropdown-pages', 'radio', 'select',
        ],
        'special' => [
            'background_position', 'background-position', 'code_editor', 'code-editor', 'editor',
            'color', 'cropped_image', 'cropped-image', 'date_time', 'date-time', 'image', 'media',
            'nav_menu_auto_add', 'nav-menu-auto-add', 'nav_menu', 'nav-menu', 'nav_menu_item', 'nav-menu-item',
            'nav_menu_location', 'nav-menu-location', 'nav_menu_locations', 'nav-menu-locations',
            'nav_menu_name', 'nav-menu-name', 'theme', 'sidebar_widgets', 'sidebar-widgets',
            'widget_form', 'widget-form',
        ],
    ];

    /**
     * @var array
     */
    protected $custom_types = ['control', 'section'];

    /**
     * @var array
     */
    protected $default_transports = ['refresh', 'postMessage'];

    /**
     * @var array
     */
    protected $default_types = ['option', 'theme_mod'];

    /**
     * @var array
     */
    protected $mime_types = ['image', 'audio', 'video', 'application', 'text'];

    /**
     * @var string
     */
    protected $preview = '';

    /**
     * Constructor.
     *
     * @param  array   $customtypes
     */
    public function __construct($customtypes = [])
    {
        // Initialize CustomizerModel
        $this->model = new CustomizerModel();

        // Initialize custom types
        if (!empty($customtypes)) {
            foreach ($customtypes['controls'] as $name) {
                $this->addCustomType($name);
            }

            foreach ($customtypes['sections'] as $name) {
                $this->addCustomType($name, 'section');
            }
        }

        // Add pages and more
        $this->setVars();
        $this->register();
    }

    /**
     * Adds a new value of control.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $classname
     *
     * @throws CustomizerException
     */
    public function addControl($identifier, $options, $classname = '') : void
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.control_identifier_is_empty'));
        }

        // Get control to know if identifier is already used or not
        $control = $this->getModel()->getControls($identifier);

        if (!empty($control)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.control_identifier_is_already_used'),
                $identifier
            ));
        }

        // Merge options with defaults
        $options = array_merge([
            'label'           => Translate::t('customizer.labels.control_title'),
            'description'     => '',
            'active_callback' => '',
            'allow_addition'  => false,
            'capability'      => '',
            'choices'         => [],
            'input_attrs'     => [],
            'instance_number' => 0,
            'priority'        => 10,
            'section'         => '',
            'settings'        => [],
            'type'            => 'text',
        ], $options);

        // Check section
        if (empty($options['section'])) {
            throw new CustomizerException(Translate::t('customizer.errors.control_section_is_required'));
        }

        // Add custom classname
        $options['_classname'] = $this->getClassname($classname);

        // Add control options
        $options = $this->getControlOptions($options);

        // Check section
        $section = $this->getModel()->getSections($options['section']);

        if (empty($section)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.control_section_does_not_exist'),
                $options['section']
            ));
        }

        // Get settings depending on settings option
        $options['settings'] = $this->getControlSettings($options['settings']);
        $types = array_merge($this->available_types['default'], $this->available_types['special']);

        // Check type
        if (empty($options['_classname']) && !in_array($options['type'], $types)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.control_type_is_unknown'),
                $options['type'],
                implode('</code>, <code>', $types)
            ));
        }

        // Add control
        $this->getModel()->setControls($identifier, $options);
    }

    /**
     * Register a new custom control/section type.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $path
     *
     * @throws CustomizerException
     */
    public function addCustomType($name, $type = 'control', $path = '') : void
    {
        // Check name
        if (empty($name)) {
            throw new CustomizerException(Translate::t('customizer.errors.customtype_name_is_empty'));
        }

        // Check type
        if (!in_array($type, $this->custom_types)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.customtype_type_is_unknown'),
                $type,
                implode('</code>, <code>', $this->custom_types)
            ));
        }

        $file = '';

        // Get custom type to know if identifier is already used or not
        $customtype = $this->getModel()->getCustomTypes($name);

        // Check custom type
        if (!empty($customtype)) {
            if (!empty($path)) {
                throw new CustomizerException(sprintf(
                    Translate::t('customizer.errors.customtype_name_is_already_used'),
                    $name
                ));
            }

            return;
        }

        // Check path
        if (!empty($path) && !file_exists($file = realpath($path))) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.customtype_path_does_not_exists'),
                $name
            ));
        }

        // Add custom type
        $this->getModel()->setCustomTypes($name, [
            'file' => $file,
            'type' => $type
        ]);
    }

    /**
     * Adds a new value of panel.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $page_redirect
     *
     * @throws CustomizerException
     */
    public function addPanel($identifier, $options, $page_redirect = '') : void
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.panel_identifier_is_empty'));
        }

        // Get panel to know if identifier is already used or not
        $panel = $this->getModel()->getPanels($identifier);

        if (!empty($panel)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.panel_identifier_is_already_used'),
                $identifier
            ));
        }

        // Merge options with defaults
        $options = array_merge([
            'title'           => Translate::t('customizer.labels.panel_title'),
            'description'     => '',
            'priority'        => 160,
            'capability'      => 'edit_theme_options',
            'theme_supports'  => '',
            'type'            => '',
            'active_callback' => [],
        ], $options);

        // Check page redirect
        $options['_redirect'] = '';

        if (!empty($page_redirect)) {
            $options['_redirect'] = $page_redirect;
        }

        // Add panel
        $this->getModel()->setPanels($identifier, $options);
    }

    /**
     * Adds a new value of section.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  string  $classname
     *
     * @throws CustomizerException
     */
    public function addSection($identifier, $options, $classname = '') : void
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.section_identifier_is_empty'));
        }

        // Get section to know if identifier is already used or not
        $section = $this->getModel()->getSections($identifier);

        if (!empty($section)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.section_identifier_is_already_used'),
                $identifier
            ));
        }

        // Get panel depending on panel option
        if (isset($options['panel'])) {
            $panel = $this->getModel()->getPanels($options['panel']);

            // Check panel
            if (empty($panel)) {
                throw new CustomizerException(sprintf(
                    Translate::t('customizer.errors.section_panel_does_not_exist'),
                    $options['panel']
                ));
            }
        }

        // Merge options with defaults
        $options = array_merge([
            'title'              => Translate::t('customizer.labels.section_title'),
            'description'        => '',
            'active_callback'    => [],
            'capability'         => 'edit_theme_options',
            'description_hidden' => false,
            'panel'              => '',
            'priority'           => 160,
            'theme_supports'     => '',
            'type'               => '',
        ], $options);

        // Add custom classname
        $options['_classname'] = $this->getClassname($classname);

        // Add section
        $this->getModel()->setSections($identifier, $options);
    }

    /**
     * Adds a new value of setting.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @throws CustomizerException
     */
    public function addSetting($identifier, $options) : void
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.setting_identifier_is_empty'));
        }

        // Get setting to know if identifier is already used or not
        $setting = $this->getModel()->getSettings($identifier);

        if (!empty($setting)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.setting_identifier_is_already_used'),
                $identifier
            ));
        }

        // Merge options with defaults
        $options = array_merge([
            'capability'           => 'edit_theme_options',
            'default'              => null,
            'dirty'                => false,
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
            'theme_supports'       => '',
            'transport'            => 'refresh',
            'type'                 => 'theme_mod',
            'validate_callback'    => '',
        ], $options);

        // Check type
        if (!in_array($options['type'], $this->default_types)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.setting_type_is_unknown'),
                $options['type'],
                implode('</code>, <code>', $this->default_types)
            ));
        }

        // Check transport
        if (!in_array($options['transport'], $this->default_transports)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.setting_transport_is_unknown'),
                $options['transport'],
                implode('</code>, <code>', $this->default_transports)
            ));
        }

        // Add setting
        $this->getModel()->setSettings($identifier, $options);
    }

    /**
     * Get control options.
     *
     * @param  string  $classname
     *
     * @return string
     */
    protected function getClassname($classname) : string
    {
        if (empty($classname)) {
            return '';
        }

        // Get custom type to know if classname is already used or not
        $customtype = $this->getModel()->getCustomTypes($classname);

        if (empty($customtype)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.customtype_does_not_exist'),
                $classname
            ));
        }

        // Add custom classname
        return $classname;
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
        // Check control type
        if (in_array($options['type'], $this->available_types['default'])) {
            // Check options
            $options['choices']     = isset($options['choices']) ? $options['choices'] : [];
            $options['input_attrs'] = isset($options['input_attrs']) ? $options['input_attrs'] : [];

            return $options;
        }

        switch ($options['type']) {
            case 'background_position':
            case 'background-position':
                // WP_Customize_Background_Position_Control
                $options['_classname'] = 'WP_Customize_Background_Position_Control';
                break;
            case 'code_editor':
            case 'code-editor':
            case 'editor':
                // WP_Customize_Code_Editor_Control
                $options['_classname'] = 'WP_Customize_Code_Editor_Control';
                break;
            case 'color':
                // WP_Customize_Color_Control
                $options['_classname'] = 'WP_Customize_Color_Control';
                break;
            case 'cropped_image':
            case 'cropped-image':
                // WP_Customize_Cropped_Image_Control
                $options['_classname'] = 'WP_Customize_Cropped_Image_Control';

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
                break;
            case 'date_time':
            case 'date-time':
                // WP_Customize_Date_Time_Control
                $options['_classname'] = 'WP_Customize_Date_Time_Control';

                $options['allow_past_date'] = isset($options['allow_past_date']) ? $options['allow_past_date'] : true;
                $options['include_time']    = isset($options['include_time']) ? $options['include_time'] : true;
                $options['max_year']        = isset($options['max_year']) ? $options['max_year'] : '9999';
                $options['min_year']        = isset($options['min_year']) ? $options['min_year'] : '1000';
                $options['twelve_hour_format'] = isset($options['twelve_hour_format'])
                    ? $options['twelve_hour_format']
                    : false;
                break;
            case 'image':
                // WP_Customize_Image_Control
                $options['_classname'] = 'WP_Customize_Image_Control';

                $options['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
                    'select'       => Translate::t('customizer.labels.control_image_select'),
                    'change'       => Translate::t('customizer.labels.control_image_change'),
                    'default'      => Translate::t('customizer.labels.control_image_default'),
                    'remove'       => Translate::t('customizer.labels.control_image_remove'),
                    'placeholder'  => Translate::t('customizer.labels.control_image_placeholder'),
                    'frame_title'  => Translate::t('customizer.labels.control_image_frame_title'),
                    'frame_button' => Translate::t('customizer.labels.control_image_frame_button'),
                ];
                break;
            case 'media':
                // WP_Customize_Media_Control
                $options['_classname'] = 'WP_Customize_Media_Control';

                $options['mime_type'] = isset($options['mime_type']) ? $options['mime_type'] : 'image';
                $options['mime_type'] = !in_array($options['mime_type'], $this->mime_types)
                    ? 'image'
                    : $options['mime_type'];

                $options['button_labels'] = isset($options['button_labels']) ? $options['button_labels'] : [
                    'select'       => Translate::t('customizer.labels.control_media_select'),
                    'change'       => Translate::t('customizer.labels.control_media_change'),
                    'default'      => Translate::t('customizer.labels.control_media_default'),
                    'remove'       => Translate::t('customizer.labels.control_media_remove'),
                    'placeholder'  => Translate::t('customizer.labels.control_media_placeholder'),
                    'frame_title'  => Translate::t('customizer.labels.control_media_frame_title'),
                    'frame_button' => Translate::t('customizer.labels.control_media_frame_button'),
                ];
            case 'nav_menu_auto_add':
            case 'nav-menu-auto-add':
                // WP_Customize_Nav_Menu_Auto_Add_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Auto_Add_Control';
                break;
            case 'nav_menu':
            case 'nav-menu':
                // WP_Customize_Nav_Menu_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Control';
                break;
            case 'nav_menu_item':
            case 'nav-menu-item':
                // WP_Customize_Nav_Menu_Item_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Item_Control';
                break;
            case 'nav_menu_location':
            case 'nav-menu-location':
                // WP_Customize_Nav_Menu_Location_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Location_Control';
                break;
            case 'nav_menu_locations':
            case 'nav-menu-locations':
                // WP_Customize_Nav_Menu_Locations_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Locations_Control';
                break;
            case 'nav_menu_name':
            case 'nav-menu-name':
                // WP_Customize_Nav_Menu_Name_Control
                $options['_classname'] = 'WP_Customize_Nav_Menu_Name_Control';
                break;
            case 'theme':
                // WP_Customize_Theme_Control
                $options['_classname'] = 'WP_Customize_Theme_Control';
                break;
            case 'sidebar_widgets':
            case 'sidebar-widgets':
                // WP_Widget_Area_Customize_Control
                $options['_classname'] = 'WP_Widget_Area_Customize_Control';
                break;
            case 'widget_form':
            case 'widget-form':
                // WP_Widget_Form_Customize_Control
                $options['_classname'] = 'WP_Widget_Form_Customize_Control';
                break;
        }

        $this->addCustomType($options['_classname'], 'control', '');
        unset($options['type']);

        return $options;
    }

    /**
     * Get control settings.
     *
     * @param  array   $settings
     *
     * @return mixed
     */
    protected function getControlSettings($settings)
    {
        if (empty($settings)) {
            return [];
        }

        if (is_array($settings)) {
            $return = [];

            // Iterate on settings
            foreach ($settings as $id => $name) {
                $name = Helpers::urlize($name);
                $return[$id] = $name;

                // Check setting
                $setting = $this->getModel()->getSettings($name);

                if (empty($setting)) {
                    throw new CustomizerException(sprintf(
                        Translate::t('customizer.errors.control_settings_does_not_exist'),
                        $name
                    ));
                }
            }

            return $return;
        }

        //$settings = Helpers::urlize($settings);

        // Check setting
        $setting = $this->getModel()->getSettings($settings);

        if (empty($setting)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.control_settings_does_not_exist'),
                $settings
            ));
        }

        return $settings;
    }

    /**
     * Return preview scripts.
     *
     * @return string
     */
    public function getPreviewScript() : string
    {
        return $this->preview;
    }

    /**
     * Register customizer.
     *
     * @throws CustomizerException
     */
    protected function register() : void
    {
        // Initialize hook
        new CustomizerHook($this);
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars() : void;
}
