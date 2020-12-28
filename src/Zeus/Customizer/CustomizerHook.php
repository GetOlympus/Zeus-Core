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
    protected $customtypes = [];

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
                    'path'       => $homeurl,
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
        add_action('customize_register', [$this, 'customizeControls'], 10, 1);
        add_action('customize_register', [$this, 'customizeRegister'], 11, 1);

        // Load customizer assets
        add_action('customize_preview_init', [$this, 'scriptsPreview']);
        add_action('customize_controls_enqueue_scripts', [$this, 'scriptsEnqueue'], 7);
    }

    /**
     * Customize and manipulate the Theme Customization admin screen.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
     *
     * @param  object  $wp_customize
     *
     * @throws CustomizerException
     */
    public function customizeControls($wp_customize) : void
    {
        /**
         * Fires before controls through customizer.
         *
         * @param  object  $wp_customize
         * @param  object  $this
         */
        do_action('ol.zeus.customizerhook_controls_before', $wp_customize, $this);

        $customtypes = $this->customizer->getModel()->getCustomTypes();

        // Check custom types
        if (empty($customtypes)) {
            return;
        }

        // Iterate on all custom types
        foreach ($customtypes as $name => $opts) {
            if (in_array($name, $this->customtypes)) {
                continue;
            }

            $this->customtypes[] = $name;

            // WordPress default custom types don't need any extra files
            if (!empty($opts['file'])) {
                require_once $opts['file'];
            }

            // Register custom type depending on type
            if ('section' === $opts['type']) {
                $wp_customize->register_section_type($name);
                continue;
            }

            $wp_customize->register_control_type($name);
        }

        /**
         * Fires after controls through customizer.
         *
         * @param  object  $wp_customize
         * @param  object  $this
         */
        do_action('ol.zeus.customizerhook_controls_after', $wp_customize, $this);
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
        /**
         * Fires before registering contents through customizer.
         *
         * @param  object  $wp_customize
         * @param  object  $this
         */
        do_action('ol.zeus.customizerhook_register_before', $wp_customize, $this);

        // Adds everything needed
        $this->addPanels($wp_customize);
        $this->addSections($wp_customize);
        $this->addSettings($wp_customize);
        $this->addControls($wp_customize);

        /**
         * Fires after registering contents through customizer.
         *
         * @param  object  $wp_customize
         * @param  object  $this
         */
        do_action('ol.zeus.customizerhook_register_after', $wp_customize, $this);
    }

    /**
     * Adds controls.
     *
     * @param  object  $wp_customize
     */
    protected function addControls($wp_customize) : void
    {
        $controls = $this->customizer->getModel()->getControls();

        // Check controls
        if (empty($controls)) {
            return;
        }

        // Iterate on all controls
        foreach ($controls as $id => $options) {
            $classname = $options['_classname'];
            unset($options['_classname']);

            if (empty($classname)) {
                $wp_customize->add_control($id, $options);
                continue;
            }

            // Check custom classname
            if (!in_array($classname, $this->customtypes)) {
                continue;
            }

            $wp_customize->add_control(new $classname($wp_customize, $id, $options));
        }
    }

    /**
     * Adds panels.
     *
     * @param  object  $wp_customize
     */
    protected function addPanels($wp_customize) : void
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
     * Adds sections.
     *
     * @param  object  $wp_customize
     */
    protected function addSections($wp_customize) : void
    {
        $sections = $this->customizer->getModel()->getSections();

        // Check sections
        if (empty($sections)) {
            return;
        }

        // Iterate on all sections
        foreach ($sections as $id => $options) {
            $classname = $options['_classname'];
            unset($options['_classname']);

            if (empty($classname)) {
                $wp_customize->add_section($id, $options);
                continue;
            }

            // Check custom classname
            if (!in_array($classname, $this->customtypes)) {
                continue;
            }

            $wp_customize->add_section(new $classname($wp_customize, $id, $options));
        }
    }

    /**
     * Adds settings.
     *
     * @param  object  $wp_customize
     */
    protected function addSettings($wp_customize) : void
    {
        $settings = $this->customizer->getModel()->getSettings();

        // Check settings
        if (empty($settings)) {
            return;
        }

        // Iterate on all settings
        foreach ($settings as $id => $options) {
            $wp_customize->add_setting($id, $options);
        }
    }

    /**
     * Enqueue scripts.
     */
    public function scriptsEnqueue() : void
    {
        $customtypes = $this->customizer->getModel()->getCustomTypes();

        // Check custom types
        if (empty($customtypes)) {
            return;
        }

        // Iterate on all custom types
        foreach ($customtypes as $name => $opts) {
            if (!method_exists($name, 'assets')) {
                continue;
            }

            $name::assets();
        }
    }

    /**
     * Preview styles.
     */
    public function scriptsPreview() : void
    {
        if (!is_customize_preview()) {
            return;
        }

        $preview = $this->customizer->getPreviewScript();

        if (empty($preview)) {
            return;
        }

        $name = explode('\\', get_class($this->customizer));
        $file = Helpers::urlize(array_pop($name));

        // Enqueue scripts and set usefull urls
        Helpers::enqueueFiles([$file.'-preview' => ['src' => $preview]], 'js', ['jquery', 'customize-preview']);
    }
}
