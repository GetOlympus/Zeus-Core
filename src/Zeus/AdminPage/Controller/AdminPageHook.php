<?php

namespace GetOlympus\Zeus\AdminPage\Controller;

use GetOlympus\Zeus\AdminPage\Controller\AdminPageField;
use GetOlympus\Zeus\AdminPage\Controller\AdminPageHookInterface;
use GetOlympus\Zeus\Field\Controller\Field;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Works with AdminPage Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
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
     * @var string
     */
    protected $parent;

    /**
     * Constructor.
     *
     * @param string    $currentPage
     * @param string    $currentSection
     * @param string    $parent
     * @param array     $options
     */
    public function __construct($currentPage, $currentSection, $parent, $options)
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
        $this->parent = $parent;

        $this->init();
    }

    /**
     * Initialize menu pages.
     */
    public function init()
    {
        // Get options
        $filter_slug = $this->currentPage;
        $fields = isset($this->options['fields']) ? $this->options['fields'] : [];

        // Check sections
        if (!empty($this->options['sections']) && is_array($this->options['sections'])) {
            foreach ($this->options['sections'] as $sectionSlug => $sectionName) {
                if ($sectionSlug !== $this->currentSection) {
                    continue;
                }

                if (isset($this->options['sections'][$this->currentSection]['fields'])) {
                    $fields = $this->options['sections'][$this->currentSection]['fields'];
                }

                $filter_slug .= '-'.$this->currentSection;
                break;
            }
        }

        /**
         * Build page contents.
         *
         * @var     string  $currentPage
         * @param   array   $contents
         * @return  array   $contents
         */
        $this->fields = apply_filters('ol_zeus_adminpage_'.$filter_slug.'_contents', $fields);

        // Display main render
        $this->renderFields();
    }

    /**
     * Get section fields.
     */
    public function renderFields()
    {
        // Save fields in DB
        $this->saveFields();

        // Get contents
        $pageCurrent = $this->currentPage;
        $sectionCurrent = $this->currentSection;
        $formFields = $this->fields;
        $contentOptions = $this->options;

        // Get links
        $u_parent = !empty($this->parent) ? $this->parent : 'admin.php';
        $u_link = 'page='.$pageCurrent;
        $u_section = !empty($sectionCurrent) ? '&section='.$sectionCurrent : '';

        // Work on vars
        $vars = [
            'title'         => $contentOptions['title'],
            'description'   => $contentOptions['description'],
            'submit'        => $contentOptions['submit'],

            // Texts and URLs
            't_submit'      => Translate::t('adminpage.submit'),
            'u_link'        => $u_link,
            'u_action'      => admin_url($u_parent.'?'.$u_link.$u_section),
        ];

        // Display sections
        if (!empty($contentOptions['sections']) && is_array($contentOptions['sections'])) {
            foreach ($contentOptions['sections'] as $slug => $opts) {
                // Update option
                $opts['slug'] = $slug;
                $opts['u_link'] = admin_url($u_parent.'?'.$u_link.'&section='.$slug);

                // Update vars
                $vars['sections'][] = $opts;

                if ($slug !== $sectionCurrent) {
                    continue;
                }

                // Update vars
                $vars['submit'] = $opts['submit'];
            }
        }

        // Work on current vars
        $vars['c_page'] = $pageCurrent;
        $vars['c_section'] = $sectionCurrent;

        // Display fields
        if (!empty($formFields)) {
            foreach ($formFields as $field) {
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
