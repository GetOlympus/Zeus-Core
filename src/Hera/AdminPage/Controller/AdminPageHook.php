<?php

namespace GetOlympus\Hera\AdminPage\Controller;

use GetOlympus\Hera\AdminPage\Controller\AdminPageHookInterface;
use GetOlympus\Hera\Field\Controller\Field;
use GetOlympus\Hera\Option\Controller\Option;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Works with AdminPage Engine.
 *
 * @package Olympus Hera
 * @subpackage AdminPage\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.7
 *
 */

class AdminPageHook implements AdminPageHookInterface
{
    /**
     * @var string
     */
    protected $currentPage;

    /**
     * @var string
     */
    protected $currentSection;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param string    $currentPage
     * @param string    $currentSection
     * @param array     $options
     */
    public function __construct($currentPage, $currentSection, $options)
    {
        // Check current page
        if (empty($currentPage)) {
            return;
        }

        // Define a current section if needed
        if (!empty($options['sections']) && is_array($options['sections'])) {
            reset($options['sections']);
            $currentSection = empty($currentSection) ? key($options['sections']) : $currentSection;
        }

        $this->currentPage = $currentPage;
        $this->currentSection = $currentSection;
        $this->options = $options;

        $this->init();
    }

    /**
     * Initialize menu pages.
     */
    public function init()
    {
        // Get options
        $options = $this->options;

        // Check sections
        if (!empty($options['sections']) && is_array($options['sections'])) {
            $fields = $this->getSectionFields();
        } else {
            $fields = $this->getPageFields();
        }

        $this->fields = $fields;

        // Display main render
        $this->renderFields();
    }

    /**
     * Get page fields.
     */
    public function getPageFields()
    {
        // Get current page
        $currentPage = $this->currentPage;

        /**
         * Build page contents.
         *
         * @var     string  $currentPage
         * @param   array   $contents
         * @return  array   $contents
         */
        return apply_filters('olh_adminpage_'.$currentPage.'_contents', []);
    }

    /**
     * Get section fields.
     */
    public function getSectionFields()
    {
        // Get contents
        $currentPage = $this->currentPage;
        $currentSection = $this->currentSection;
        $options = $this->options;

        // Get all datas
        foreach ($options['sections'] as $sectionSlug => $sectionName) {
            if ($sectionSlug !== $currentSection) {
                continue;
            }

            /**
             * Build section page contents.
             *
             * @var     string  $currentPage
             * @var     string  $currentSection
             * @param   array   $contents
             * @return  array   $contents
             */
            return apply_filters('olh_adminpage_'.$currentPage.'-'.$currentSection.'_contents', []);
        }
    }

    /**
     * Get section fields.
     */
    public function renderFields()
    {
        // Save fields in DB
        $this->saveFields();

        // Get contents
        $currentPage = $this->currentPage;
        $currentSection = $this->currentSection;
        $fields = $this->fields;
        $options = $this->options;

        // Get links
        $u_link = 'page='.$currentPage;
        $u_section = !empty($currentSection) ? '&section='.$currentSection : '';

        // Work on vars
        $vars = [
            'title'         => $options['title'],
            'description'   => $options['description'],
            'submit'        => $options['submit'],

            // Texts and URLs
            't_submit'      => Translate::t('adminpage.submit'),
            'u_link'        => $u_link,
            'u_action'      => admin_url('admin.php?'.$u_link.$u_section),
        ];

        // Display sections
        if (!empty($options['sections']) && is_array($options['sections'])) {
            foreach ($options['sections'] as $slug => $opts) {
                // Update option
                $opts['slug'] = $slug;
                $opts['u_link'] = admin_url('admin.php?'.$u_link.'&section='.$slug);

                // Update vars
                $vars['sections'][] = $opts;

                if ($slug !== $currentSection) {
                    continue;
                }

                // Update vars
                $vars['submit'] = $opts['submit'];
            }
        }

        // Work on current vars
        $vars['c_page'] = $currentPage;
        $vars['c_section'] = $currentSection;

        // Display fields
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (!$field) {
                    continue;
                }

                $vars['fields'][] = $field->render([], [
                    'template' => 'adminpage',
                ], false);
            }
        }

        // Render view
        Render::view('adminpage.html.twig', $vars, 'adminpage');
    }

    /**
     * Set section fields.
     */
    public function saveFields()
    {
        if (empty($this->fields)) {
            return;
        }

        $ids = [];

        // Retrieve all fields ids
        foreach ($this->fields as $field) {
            if (!$field) {
                continue;
            }

            // Build contents
            $ctn = (array) $field->getModel()->getContents();
            $hasId = (boolean) $field->getModel()->getHasId();

            // Check ID
            if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                continue;
            }

            $ids[] = $ctn['id'];
        }

        $this->saveRequest($ids);
        $this->saveFiles($ids);
    }

    /**
     * Save files.
     *
     * @param array $ids
     */
    public function saveFiles($ids)
    {
        // Work on $_FILES
        $files = $_FILES;

        if (empty($files) || empty($ids)) {
            return;
        }

        // Get required files
        require_once ABSPATH.'wp-admin'.S.'includes'.S.'image.php';
        require_once ABSPATH.'wp-admin'.S.'includes'.S.'file.php';
        require_once ABSPATH.'wp-admin'.S.'includes'.S.'media.php';

        // Iterate
        foreach ($files as $k => $v) {
            // Don't do nothing if no file is defined
            if (empty($v['tmp_name']) || !in_array($k, $ids)) {
                continue;
            }

            // Do the magic
            $file = wp_handle_upload($v, ['test_form' => false]);

            // Register settings
            Option::set($k, $file['url']);
        }
    }

    /**
     * Save request.
     *
     * @param array $ids
     */
    public function saveRequest($ids)
    {
        // Work on $_POST
        $request = $_POST;

        if (empty($request) || empty($ids) || !isset($request['updated']) || 'true' !== $request['updated']) {
            return;
        }

        // Iterate
        foreach ($request as $k => $v) {
            // Don't register this default value
            if (in_array($k, ['updated','submit']) || !in_array($k, $ids)) {
                continue;
            }

            // Register settings
            Option::set($k, $v);
        }
    }
}
