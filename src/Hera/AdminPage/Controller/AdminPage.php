<?php

namespace GetOlympus\Hera\AdminPage\Controller;

use GetOlympus\Hera\AdminPage\Controller\AdminPageHook;
use GetOlympus\Hera\AdminPage\Controller\AdminPageInterface;
use GetOlympus\Hera\AdminPage\Exception\AdminPageException;
use GetOlympus\Hera\AdminPage\Model\AdminPageModel;
use GetOlympus\Hera\Base\Controller\Base;
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

abstract class AdminPage extends Base implements AdminPageInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize AdminPageModel
        $this->model = new AdminPageModel();

        // Initialize variables and filters
        $this->setVars();
        $this->setFilters();

        // Work on admin only
        if (OLH_ISADMIN) {
            $this->init();
            $this->initAssets();
        }
    }

    /**
     * Build AdminPageModel and initialize admin pages.
     */
    public function init()
    {
        $pages = $this->getModel()->getPages();

        // Check datas
        if (empty($pages)) {
            return;
        }

        global $admin_page_hooks;

        reset($pages);

        // Update identifier
        $identifier = Render::urlize(key($pages));
        $this->getModel()->setIdentifier($identifier);

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
     * Initialize assets in admin pages.
     */
    public function initAssets()
    {
        // Get current page
        $currentPage = Request::get('page');
        $currentSection = Request::get('section');
        $identifier = $this->getModel()->getIdentifier();
        $page_id = str_replace($identifier.'-', '', $currentPage);

        // Check current page
        if (!$this->getModel()->hasPage($page_id)) {
            return;
        }

        // Build page details
        $optionPage = $this->getModel()->getPages($page_id);
        $currentSection = !empty($optionPage['sections']) && empty($currentSection) ? key($optionPage['sections']) : $currentSection;
        $filter_slug = empty($currentSection) ? $currentPage : $currentPage.'-'.$currentSection;

        /**
         * Build page contents.
         *
         * @var     string  $currentPage
         * @param   array   $contents
         * @return  array   $contents
         */
        $fields = apply_filters('olh_adminpage_'.$filter_slug.'_contents', []);

        // Render assets
        Render::assets(['admin.php'], $fields);
    }

    /**
     * Add root admin page.
     */
    public function addRootPage()
    {
        $identifier = $this->getModel()->getIdentifier();

        // Check page
        if (!$this->getModel()->hasPage($identifier)) {
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
        $options = $this->getModel()->getPages($identifier);
        $options = array_merge($defaults, $options);
        $this->getModel()->updatePage($identifier, $options);

        // Add main page
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
        $identifier = $this->getModel()->getIdentifier();
        $options = $this->getModel()->getPages($identifier);

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
        $identifier = $this->getModel()->getIdentifier();
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
        $this->getModel()->updatePage($slug, $options);

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
        $identifier = $this->getModel()->getIdentifier();

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
        $identifier = $this->getModel()->getIdentifier();
        $currentPage = Request::get('page');
        $currentSection = Request::get('section');

        $page_id = str_replace($identifier.'-', '', $currentPage);

        // Check current page
        if (!$this->getModel()->hasPage($page_id)) {
            return;
        }

        // Store details
        $options = $this->getModel()->getPages($page_id);

        // Works on hook
        $hook = new AdminPageHook($currentPage, $currentSection, $options);
        $this->getModel()->setHook($hook);
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
