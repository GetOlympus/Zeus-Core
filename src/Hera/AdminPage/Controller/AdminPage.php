<?php

namespace GetOlympus\Hera\AdminPage\Controller;

use GetOlympus\Hera\AdminPage\Controller\AdminPageHook;
use GetOlympus\Hera\AdminPage\Controller\AdminPageInterface;
use GetOlympus\Hera\AdminPage\Exception\AdminPageException;
use GetOlympus\Hera\AdminPage\Model\AdminPageModel;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Gets its own admin page.
 *
 * @package Olympus Hera
 * @subpackage AdminPage\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.7
 *
 */

abstract class AdminPage implements AdminPageInterface
{
    /**
     * @var AdminPageModel
     */
    protected $adminpage;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize AdminPageModel
        $this->adminpage = new AdminPageModel();

        // Initialize variables and filters
        $this->setVars();
        $this->setFilters();

        // Work on admin only
        if (OLH_ISADMIN) {
            $this->init();
        }
    }

    /**
     * Build AdminPageModel and initialize admin pages.
     */
    public function init()
    {
        $pages = $this->adminpage->getPages();

        // Check datas
        if (empty($pages)) {
            return;
        }

        global $admin_page_hooks;

        reset($pages);

        // Update identifier
        $identifier = Render::urlize(key($pages));
        $this->adminpage->setIdentifier($identifier);

        // Add root single menu if identifier is unknown
        if (!isset($admin_page_hooks[$identifier])) {
            $this->addRootPage();
        }

        // Iterate on pages
        foreach ($pages as $slug => $options) {
            if (empty($options)) {
                continue;
            }

            // Get slug
            $slug = Render::urlize($slug);

            // Add child menu
            $this->addChild($slug, $options);
        }
    }

    /**
     * Add root admin page.
     */
    public function addRootPage()
    {
        $identifier = $this->adminpage->getIdentifier();

        // Check page
        if (!$this->adminpage->hasPage($identifier)) {
            throw new AdminPageException(sprintf(Translate::t('adminpage.errors.page_is_empty'), $identifier));
        }

        // Set default option's values
        $defaults = [
            'slug'          => $identifier,

            // Page options
            'title'         => Translate::t('adminpage.root.defaults.title'),
            'name'          => Translate::t('adminpage.root.defaults.name'),
            'icon'          => 'dashicons-admin-generic',
            'description'   => '',
            'position'      => 80,
            'sections'      => [],

            // Options
            'capabilities'  => OLH_WP_CAP,
            'adminbar'      => true,
            'submit'        => true,
        ];

        // Update pages
        $options = $this->adminpage->getPages($identifier);
        $options = array_merge($defaults, $options);
        $this->adminpage->updatePage($identifier, $options);

        // Add main page
        // @todo add_posts_page
        add_menu_page(
            $options['title'],
            $options['name'],
            $options['capabilities'],
            $identifier,
            [&$this, 'callback'],
            $options['icon'],
            $options['position']
        );

        // Add admin bar menu
        if ($options['adminbar']) {
            $this->addRootAdminBar();
        }
    }

    /**
     * Add root admin bar page.
     */
    public function addRootAdminBar()
    {
        $identifier = $this->adminpage->getIdentifier();
        $options = $this->adminpage->getPages($identifier);

        add_action('admin_bar_menu', function () use ($identifier, $options) {
            global $wp_admin_bar;

            // Add main admin bar
            $wp_admin_bar->add_node([
                'parent'    => '',
                'id'        => $identifier,
                'title'     => $options['title'],
                'href'      => admin_url('admin.php?page='.$identifier),
                'meta'      => false
            ]);
        });
    }

    /**
     * Add child admin page.
     *
     * @param string    $slug
     * @param array     $options
     */
    public function addChild($slug, $options)
    {
        $identifier = $this->adminpage->getIdentifier();
        // Check slug
        if (empty($slug)) {
            throw new AdminPageException(Translate::t('adminpage.errors.slug_is_not_defined'));
        }

        // Set default option's values
        $defaults = [
            'slug'          => $slug,

            // Page options
            'title'         => Translate::t('adminpage.child.defaults.title'),
            'name'          => Translate::t('adminpage.child.defaults.name'),
            'description'   => '',
            'sections'      => [],

            // Options
            'capabilities'  => OLH_WP_CAP,
            'adminbar'      => true,
            'submit'        => true,
        ];

        // Merge options
        $options = array_merge($defaults, $options);
        $this->adminpage->updatePage($slug, $options);

        $pageid = $slug === $identifier ? $identifier : $identifier.'-'.$slug;

        // Add child page
        add_submenu_page(
            $identifier,
            $options['title'],
            $options['name'],
            $options['capabilities'],
            $pageid,
            [&$this, 'callback']
        );

        // Add admin bar menu
        if ($options['adminbar']) {
            $this->addChildAdminBar($slug, $options);
        }
    }

    /**
     * Add child admin bar page.
     *
     * @param string    $slug
     * @param array     $options
     */
    public function addChildAdminBar($slug, $options)
    {
        $identifier = $this->adminpage->getIdentifier();

        add_action('admin_bar_menu', function () use ($identifier, $slug, $options) {
            global $wp_admin_bar;

            if ($slug === $identifier) {
                return;
            }

            // Get page identifier
            $pageid = $identifier.'-'.$slug;

            // Add child admin bar
            $wp_admin_bar->add_node([
                'parent'    => $identifier,
                'id'        => $pageid,
                'title'     => $options['title'],
                'href'      => admin_url('admin.php?page='.$pageid),
                'meta'      => false
            ]);
        });
    }

    /**
     * Hook callback.
     */
    public function callback()
    {
        // Get current page and section
        $identifier = $this->adminpage->getIdentifier();
        $currentPage = Request::get('page');
        $currentSection = Request::get('section');

        $page_id = str_replace($identifier.'-', '', $currentPage);

        // Check current page
        if (!$this->adminpage->hasPage($page_id)) {
            return;
        }

        // Store details
        $options = $this->adminpage->getPages($page_id);

        // Works on hook
        $hook = new AdminPageHook($currentPage, $currentSection, $options);
        $this->adminpage->setHook($hook);
    }

    /**
     * Prepare filters.
     */
    abstract public function setFilters();

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
