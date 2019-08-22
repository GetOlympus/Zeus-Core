<?php

namespace GetOlympus\Zeus\AdminPage\Controller;

use GetOlympus\Zeus\AdminPage\Implementation\AdminPageHookImplementation;
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

class AdminPageHook implements AdminPageHookImplementation
{
    /**
     * @var AdminPage
     */
    protected $adminpage;

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
    protected $page;

    /**
     * @var boolean
     */
    protected $request;

    /**
     * @var string
     */
    protected $section;

    /**
     * Constructor.
     *
     * @param  AdminPage $adminpage
     */
    public function __construct($adminpage)
    {
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

        $page = Request::get('page');

        // Check current page
        if (empty($page)) {
            return;
        }

        $opts = $adminpage->getModel()->getPages($page);

        // Check options
        if (empty($opts)) {
            return;
        }

        $section = Request::get('section');

        // Define a current section if needed
        if (isset($opts['sections'])) {
            reset($opts['sections']);
            $section = !empty($section) && isset($opts['sections'][$section]) ? $section : key($opts['sections']);
        } else {
            $section = '';
        }

        $this->adminpage = $adminpage;
        $this->options   = $opts;
        $this->page      = $page;
        $this->request   = false;
        $this->section   = $section;

        $this->init();
    }

    /**
     * Initialize menu pages.
     */
    public function init()
    {
        // Get options
        $filter_slug = $this->page;
        $fields = isset($this->options['fields']) ? $this->options['fields'] : [];

        // Check sections
        if (!empty($this->section)) {
            $filter_slug .= '-'.$this->section;

            $fields = isset($this->options['sections'][$this->section]['fields'])
                ? $this->options['sections'][$this->section]['fields']
                : [];
        }

        /**
         * Build page contents.
         *
         * @var    string  $filter_slug
         * @param  array   $fields
         *
         * @return array
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

        $parent = $this->adminpage->getModel()->getParent();

        // Get links
        $u_parent  = !empty($parent) ? $parent : 'admin.php';
        $u_link    = 'page='.$this->page;
        $u_section = !empty($this->section) ? '&section='.$this->section : '';

        // Work on vars
        $vars = [
            'title'         => $this->options['title'],
            'description'   => $this->options['description'],
            'submit'        => $this->options['submit'],
            'request'       => $this->request ? Translate::t('adminpage.errors.successfully_updated') : false,

            // Texts and URLs
            't_submit'      => Translate::t('adminpage.labels.submit'),
            'u_link'        => $u_link,
            'u_action'      => admin_url($u_parent.'?'.$u_link.$u_section),
        ];

        // Display sections
        if (!empty($this->options['sections'])) {
            foreach ($this->options['sections'] as $slug => $opts) {
                // Update option
                $opts['slug'] = $slug;
                $opts['u_link'] = admin_url($u_parent.'?'.$u_link.'&section='.$slug);

                // Update vars
                $vars['sections'][] = $opts;

                if ($slug !== $this->section) {
                    continue;
                }

                // Update vars
                $vars['submit'] = $opts['submit'];
            }
        }

        // Work on current vars
        $vars['c_page']    = $this->page;
        $vars['c_section'] = $this->section;

        // Prepare admin scripts and styles
        $assets = [
            'scripts' => [],
            'styles'  => [],
        ];

        // Display fields
        if (!empty($this->fields)) {
            foreach ($this->fields as $field) {
                if (!$field) {
                    continue;
                }

                // Update scripts and styles
                $fieldassets = $field->assets();

                if (!empty($fieldassets)) {
                    $assets['scripts'] = array_merge($assets['scripts'], $fieldassets['scripts']);
                    $assets['styles']  = array_merge($assets['styles'], $fieldassets['styles']);
                }

                // Prepare fields to be displayed
                $vars['fields'][] = $field->prepare('adminpage');
            }
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'adminpage.html.twig', $vars, $assets);
        $render->view();
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

            $id = (string) $field->getModel()->getIdentifier();

            if (empty($id)) {
                continue;
            }

            //$field->updatePost();

            $ids[] = $id;
        }

        $this->saveRequest($ids);
        $this->saveFiles($ids);
    }

    /**
     * Save files.
     *
     * @param  array   $ids
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

        $this->request = true;
    }

    /**
     * Save request.
     *
     * @param  array   $ids
     */
    public function saveRequest($ids)
    {
        // Works on $_POST
        $request = $_POST;

        if (empty($request) || empty($ids) || !isset($request['updated']) || 'true' !== $request['updated']) {
            return;
        }

        // Iterate
        foreach ($request as $k => $v) {
            // Don't register this default value
            if (in_array($k, ['updated', 'submit']) || !in_array($k, $ids)) {
                continue;
            }

            // Register settings
            Option::set($k, $v);
        }

        $this->request = true;
    }
}
