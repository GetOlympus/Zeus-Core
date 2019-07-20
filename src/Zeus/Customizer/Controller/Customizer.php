<?php

namespace GetOlympus\Zeus\Customizer\Controller;

use GetOlympus\Zeus\Customizer\Controller\CustomizerHook;
use GetOlympus\Zeus\Customizer\Exception\CustomizerException;
use GetOlympus\Zeus\Customizer\Implementation\CustomizerImplementation;
use GetOlympus\Zeus\Customizer\Model\CustomizerModel;
use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Gets its own customizer.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

abstract class Customizer extends Base implements CustomizerImplementation
{
    /**
     * @var array
     */
    protected $available = [
        'themes', 'title_tagline', 'colors', 'header_image', 'background_image', 'static_front_page',
    ];

    /**
     * @var array
     */
    protected $available_mimetypes = ['image', 'audio', 'video', 'application', 'text'];

    /**
     * @var array
     */
    protected $available_types = [
        'text', 'email', 'url', 'number', 'hidden', 'date', 'checkbox',
        'select', 'radio', 'dropdown-pages', 'textarea', 'color',
        'media', 'image', 'cropped-image', 'date-time',
    ];

    /**
     * @var string
     */
    //protected $identifier = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize CustomizerModel
        $this->model = new CustomizerModel();

        // Work on admin only
        if (OL_ZEUS_ISADMIN) {
            // Add pages and more
            $this->setVars();
            $this->register();
        }
    }

    /**
     * Adds a new value of control.
     *
     * @param  string  $identifier
     * @param  array   $options
     * @param  array   $settings
     */
    public function addControl($identifier, $options, $settings)
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.control_identifier_is_empty'));
        }

        // Works on control identifier
        $identifier = Helpers::urlize($identifier);

        // Get control to know if identifier is already used or not
        $control = $this->getModel()->getControls($identifier);

        // Check control
        if (!empty($control)) {
            throw new CustomizerException(Translate::t('customizer.errors.control_identifier_is_already_used'));
        }

        // Merge settings with defaults
        $settings = array_merge([
            'default'              => null,
            'capability'           => 'edit_theme_options',
            'theme_supports'       => '',
            'type'                 => 'theme_mod',
            'transport'            => 'refresh',
            'validate_callback'    => '',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
            'dirty'                => false,
        ], $settings);

        // Check type
        if (!in_array($settings['type'], ['option', 'theme_mod'])) {
            throw new CustomizerException(Translate::t('customizer.errors.control_setting_type_is_unknown'));
        }

        // Check transport
        if (!in_array($settings['transport'], ['refresh', 'postMessage'])) {
            throw new CustomizerException(Translate::t('customizer.errors.control_setting_transport_is_unknown'));
        }

        // Merge options with defaults
        $options = array_merge([
            'label'           => Translate::t('customizer.labels.control_title'),
            'description'     => '',
            'setting'         => $identifier,
            'capability'      => $settings['capability'],
            'priority'        => 10,
            'type'            => 'text',
            'section'         => '',
            'choices'         => [],
            'input_attrs'     => [],
            'allow_addition'  => false,
            'active_callback' => [],
        ], $options);

        // Check section
        if (empty($options['section'])) {
            throw new CustomizerException(Translate::t('customizer.errors.control_section_is_required'));
        }

        // Get section
        $section = $this->getModel()->getSections(Helpers::urlize($options['section']));

        // Check section
        if (empty($section)) {
            throw new CustomizerException(sprintf(
                Translate::t('customizer.errors.control_section_does_not_exist'),
                $options['section']
            ));
        }

        // Check type
        if (!in_array($options['type'], $this->available_types)) {
            throw new CustomizerException(Translate::t('customizer.errors.control_type_is_unknown'));
        }

        // Update options with settings
        $options['_settings'] = $settings;

        // Add control
        $this->getModel()->setControls($identifier, $options);
    }

    /**
     * Adds a new value of panel.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function addPanel($identifier, $options)
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.panel_identifier_is_empty'));
        }

        // Works on panel identifier
        $identifier = Helpers::urlize($identifier);

        // Get panel to know if identifier is already used or not
        $panel = $this->getModel()->getPanels($identifier);

        // Check panel
        if (!empty($panel)) {
            throw new CustomizerException(Translate::t('customizer.errors.panel_identifier_is_already_used'));
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

        // Add panel
        $this->getModel()->setPanels($identifier, $options);
    }

    /**
     * Adds a new value of section.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function addSection($identifier, $options)
    {
        // Check identifier
        if (empty($identifier)) {
            throw new CustomizerException(Translate::t('customizer.errors.section_identifier_is_empty'));
        }

        // Works on section identifier
        $identifier = Helpers::urlize($identifier);

        // Get section to know if identifier is already used or not
        $section = $this->getModel()->getSections($identifier);

        // Check section
        if (!empty($section)) {
            throw new CustomizerException(Translate::t('customizer.errors.section_identifier_is_already_used'));
        }

        // Get panel depending on panel option
        if (isset($options['panel'])) {
            $panel = $this->getModel()->getPanels(Helpers::urlize($options['panel']));

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
            'priority'           => 160,
            'capability'         => 'edit_theme_options',
            'theme_supports'     => '',
            'type'               => '',
            'active_callback'    => [],
            'description_hidden' => false,
            'panel'              => '',
        ], $options);

        // Add section
        $this->getModel()->setSections($identifier, $options);
    }

    /**
     * Return available mime types.
     *
     * @return array
     */
    public function getAvailableMimetypes()
    {
        return $this->available_mimetypes;
    }

    /**
     * Return available types.
     *
     * @param  string  $type
     *
     * @return array
     */
    public function getAvailableTypes($type = '')
    {
        if ('choice' === $type) {
            return ['checkbox', 'radio', 'select'];
        }

        if ('text' === $type) {
            return ['text', 'email', 'url', 'number', 'hidden', 'date', 'textarea'];
        }

        if ('special' === $type) {
            return ['media', 'image', 'cropped-image', 'date-time'];
        }

        return $this->available_types;
    }

    /**
     * Register customizer.
     */
    public function register()
    {
        $controls = $this->getModel()->getControls();

        // Check controls
        if (empty($controls)) {
            throw new CustomizerException(Translate::t('customizer.errors.no_controls_to_display'));
        }

        // Initialize hook
        new CustomizerHook($this);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
