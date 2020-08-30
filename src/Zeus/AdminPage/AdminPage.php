<?php

namespace GetOlympus\Zeus\AdminPage;

use GetOlympus\Zeus\AdminPage\AdminPageHook;
use GetOlympus\Zeus\AdminPage\AdminPageException;
use GetOlympus\Zeus\AdminPage\AdminPageInterface;
use GetOlympus\Zeus\AdminPage\AdminPageModel;
use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Request;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own admin page.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
 *
 */

abstract class AdminPage extends Base implements AdminPageInterface
{
    /**
     * @var bool
     */
    protected $adminbar = false;

    /**
     * @var array
     */
    protected $available = [
        'options-general.php'     => 'add_options_page',
        'index.php'               => 'add_dashboard_page',
        'upload.php'              => 'add_media_page',
        'link-manager.php'        => 'add_links_page',
        'edit-comments.php'       => 'add_comments_page',
        'edit.php'                => 'add_posts_page',
        'edit.php?post_type=page' => 'add_pages_page',
        'themes.php'              => 'add_theme_page',
        'plugins.php'             => 'add_plugins_page',
        'users.php'               => 'add_users_page',
        'tools.php'               => 'add_management_page',
    ];

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $parent = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Work on admin only
        if (!OL_ZEUS_ISADMIN) {
            return;
        }

        // Initialize AdminPageModel
        $this->model = new AdminPageModel();

        // Update model
        $this->getModel()->setAdminbar($this->adminbar);
        $this->getModel()->setIdentifier(Helpers::urlize($this->identifier));
        $this->getModel()->setParent($this->parent, $this->available);

        // Add pages and more
        $this->setVars();

        // Save fields in DB
        $this->saveFields();

        // Get values
        $values = Option::getAdminOption($this->getModel()->getIdentifier());
        $this->getModel()->setValues($values);

        // Initialize ajax calls and menus
        $this->initAjax();
        $this->initMenu();
    }

    /**
     * Add admin bar page.
     *
     * @param  string  $barid
     * @param  string  $title
     * @param  string  $parent
     * @param  string  $url
     */
    protected function addAdminBar($barid, $title, $parent = '', $url = '') : void
    {
        // Set parent
        if (!empty($parent)) {
            $func = $this->functionFromParent($parent);
            $parent = !empty($func) ? $parent : 'admin.php';
        }

        // Set url
        $url = empty($url) ? admin_url($parent.'?page='.$barid) : $url;

        // Call to WordPress action to display admin bar item
        add_action('admin_bar_menu', function () use ($barid, $title, $parent, $url) {
            global $wp_admin_bar;

            // Add main admin bar
            $wp_admin_bar->add_node([
                'parent'    => $parent,
                'id'        => $barid,
                'title'     => $title,
                'href'      => $url,
                'meta'      => false
            ]);
        });
    }

    /**
     * Adds a new value of pages.
     *
     * @param  string  $pageid
     * @param  array   $options
     *
     * @throws AdminPageException
     */
    public function addPage($pageid, $options) : void
    {
        $identifier = $this->getModel()->getIdentifier();
        $parent = $this->getModel()->getParent();

        // Check options
        if (empty($options)) {
            throw new AdminPageException(sprintf(Translate::t('adminpage.errors.page_is_empty'), $identifier));
        }

        // Works on page identifier
        $func = 'addPageChild';
        $pageid = Helpers::urlize($pageid);

        // Check page type: root or child
        $pages = (array) $this->getModel()->getPages();
        $is_root = empty($pages);

        // Root case
        if ($is_root && empty($parent)) {
            $func   = 'addPageRoot';
            $pageid = $identifier;
        }

        // Add root or child page
        $this->$func($pageid, $options, $parent);
    }

    /**
     * Add child page.
     *
     * @param  string  $pageid
     * @param  array   $options
     * @param  string  $parent
     */
    protected function addPageChild($pageid, $options, $parent) : void
    {
        // Merge options with defaults
        $options = array_merge([
            'title'        => Translate::t('adminpage.labels.child_title'),
            'name'         => Translate::t('adminpage.labels.child_name'),
            'capabilities' => 'edit_posts',
            'description'  => '',
            'submit'       => true,
        ], $options);

        // Set special items
        $options['_type'] = 'child';
        $options['_identifier'] = $this->getModel()->getIdentifier();
        $options['_parent'] = $parent;
        $options['_pageid'] = $pageid;

        // Update page to pages
        $this->getModel()->setPages($pageid, $options);
    }

    /**
     * Add root page.
     *
     * @param  string  $pageid
     * @param  array   $options
     * @param  string  $parent
     */
    protected function addPageRoot($pageid, $options, $parent = '') : void
    {
        // Merge options with defaults
        $options = array_merge([
            'title'        => Translate::t('adminpage.labels.root_title'),
            'name'         => Translate::t('adminpage.labels.root_name'),
            'capabilities' => 'edit_posts',
            'icon'         => 'dashicons-admin-generic',
            'position'     => 80,
            'description'  => '',
            'submit'       => true,
        ], $options);

        // Set special items
        $options['_type'] = 'root';
        $options['_pageid'] = $pageid;

        // Update page to pages
        $this->getModel()->setPages($pageid, $options);
    }

    /**
     * Adds a new value of section.
     *
     * @param  string  $sectionid
     * @param  string  $pageid
     * @param  array   $options
     *
     * @throws AdminPageException
     */
    public function addSection($sectionid, $pageid, $options) : void
    {
        // Get page details
        $page = (array) $this->getModel()->getPages($pageid);

        if (empty($page)) {
            throw new AdminPageException(sprintf(
                Translate::t('adminpage.errors.section_cannot_be_added_to_page'),
                $sectionid,
                $pageid
            ));
        }

        // Works on section identifier
        $sectionid = Helpers::urlize($sectionid);

        // Merge options with defaults
        $options = array_merge([
            'title'       => Translate::t('adminpage.labels.section_title'),
            'name'        => Translate::t('adminpage.labels.section_name'),
            'description' => '',
            'submit'      => true,
        ], $options);

        // Add page section
        $page['sections'][$sectionid] = $options;
        $this->getModel()->setPages($pageid, $page);
    }

    /**
     * Get function to call from parent
     *
     * @param  string  $parent
     * @param  string  $function
     *
     * @return string
     */
    protected function functionFromParent($parent) : string
    {
        global $admin_page_hooks;
        return isset($admin_page_hooks[$parent]) ? $this->available[$parent] : '';
    }

    /**
     * Initialize ajax calls hooks if needed.
     */
    protected function initAjax() : void
    {
        // Get pages
        $pages = $this->getModel()->getPages();

        // Check pages
        if (empty($pages)) {
            return;
        }

        $ids = [];

        // Iterate on pages
        foreach ($pages as $pageid => $options) {
            // Check direct fields
            if (isset($options['fields']) && !empty($options['fields'])) {
                foreach ($options['fields'] as $field) {
                    $id = $field->getIdentifier();

                    if (empty($id)) {
                        continue;
                    }

                    $ids[$id] = $field;
                }
            }

            // Check sections
            if (!isset($options['sections']) || empty($options['sections'])) {
                continue;
            }

            // Iterate on sections
            foreach ($options['sections'] as $sectionid => $details) {
                // Check fields
                if (!isset($details['fields']) || empty($details['fields'])) {
                    continue;
                }

                // Iterate on fields
                foreach ($details['fields'] as $field) {
                    $id = $field->getIdentifier();

                    if (empty($id)) {
                        continue;
                    }

                    $ids[$id] = $field;
                }
            }
        }

        // Works on IDs
        foreach ($ids as $id => $field) {
            // Check ajax callback method
            if (!method_exists($field, 'ajaxCallback')) {
                continue;
            }

            // Use ajax hooks
            add_action('wp_ajax_nopriv_'.$id, [$field, 'callback']);
            add_action('wp_ajax_'.$id, [$field, 'callback']);
        }
    }

    /**
     * Initialize menus through the WordPress `admin_menu` hook.
     */
    protected function initMenu() : void
    {
        // Build menus and capabilities
        add_action('admin_menu', [$this, 'menuCallback']);
    }

    /**
     * Hook admin menu.
     */
    public function menuCallback() : void
    {
        // Get pages
        $pages = $this->getModel()->getPages();

        // Check pages
        if (empty($pages)) {
            throw new AdminPageException(Translate::t('adminpage.errors.pages_are_empty'));
        }

        $identifier = $this->getModel()->getIdentifier();
        $values     = $this->getModel()->getValues();

        // Iterate on pages
        foreach ($pages as $pageid => $options) {
            // Check depends
            if (isset($options['depends']) && !empty($options['depends'])
                && !Helpers::checkDependencies($options['depends'], $values)) {
                continue;
            }

            // Check type
            if ('root' === $options['_type']) {
                // Add root page
                add_menu_page(
                    $options['title'],
                    $options['name'],
                    $options['capabilities'],
                    $options['_pageid'],
                    [$this, 'register'],
                    $options['icon'],
                    $options['position']
                );

                // Add admin bar
                if ($this->adminbar) {
                    $this->addAdminBar($options['_pageid'], $options['title']);
                }

                continue;
            }

            // Get function
            $options['_function'] = !empty($options['_parent']) ? $this->functionFromParent($options['_parent']) : '';
            $options['_function'] = !empty($options['_function']) ? $options['_function'] : 'add_submenu_page';

            // Add child page
            if ('add_submenu_page' !== $options['_function']) {
                $options['_function'](
                    $options['title'],
                    $options['name'],
                    $options['capabilities'],
                    $options['_pageid'],
                    [$this, 'register']
                );
            } else {
                add_submenu_page(
                    $options['_identifier'],
                    $options['title'],
                    $options['name'],
                    $options['capabilities'],
                    $options['_pageid'],
                    [$this, 'register']
                );
            }

            // Add admin bar
            if ($this->adminbar) {
                $this->addAdminBar($options['_pageid'], $options['title'], $options['_parent']);
            }
        }
    }

    /**
     * Hook register.
     */
    public function register() : void
    {
        // Initialize hook
        new AdminPageHook($this);
    }

    /**
     * Set section fields.
     */
    protected function saveFields() : void
    {
        // Get pages
        $pages = $this->getModel()->getPages();

        // Check pages
        if (empty($pages)) {
            return;
        }

        $ids = [];

        // Iterate on pages
        foreach ($pages as $pageid => $options) {
            // Check direct fields
            if (isset($options['fields']) && !empty($options['fields'])) {
                foreach ($options['fields'] as $field) {
                    if (!empty($field->getIdentifier())) {
                        $ids[] = $field->getIdentifier();
                    }
                }
            }

            // Check sections
            if (!isset($options['sections']) || empty($options['sections'])) {
                continue;
            }

            // Iterate on sections
            foreach ($options['sections'] as $sectionid => $details) {
                // Check fields
                if (!isset($details['fields']) || empty($details['fields'])) {
                    continue;
                }

                // Iterate on fields
                foreach ($details['fields'] as $field) {
                    if (!empty($field->getIdentifier())) {
                        $ids[] = $field->getIdentifier();
                    }
                }
            }
        }

        $identifier = $this->getModel()->getIdentifier();

        $request = Request::save($identifier, $ids);
        $request = Request::upload($identifier, $ids) ? true : $request;
        $this->getModel()->setRequest($request);
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars() : void;
}
