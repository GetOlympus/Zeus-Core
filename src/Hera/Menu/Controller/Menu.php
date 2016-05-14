<?php

namespace GetOlympus\Hera\Menu\Controller;

use GetOlympus\Hera\Menu\Model\Menu as MenuModel;
use GetOlympus\Hera\Notification\Controller\Notification;
use GetOlympus\Hera\Request\Controller\Request;
use GetOlympus\Hera\Template\Controller\Template;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Gets its own menu.
 *
 * @package Olympus Hera
 * @subpackage Menu\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Menu
{
    /**
     * @var MenuModel
     */
    protected $menu;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize MenuModel
        $this->menu = new MenuModel();

        // Update pages
        $this->menu->setPages([]);
    }

    /**
     * Add root single menu.
     *
     * @param string $identifier
     * @param array $options
     */
    public function addRootMenu($identifier, $options)
    {
        // Set default option's values
        $defaults = [
            // Page options
            'title' => Translate::t('menu.root.defaults.title'),
            'name' => Translate::t('menu.root.defaults.name'),
            'icon' => OLH_URI.'/assets/img/hera-icon.svg',
            'position' => 80,
            'sections' => [],

            // Options
            'capabilities' => OLH_WP_CAP_MAX,
            'adminbar' => false,
        ];

        // Update vars
        $this->menu->setIdentifier($identifier);
        $this->menu->setOptions(array_merge($defaults, $options));

        // Add main page
        add_menu_page(
            $this->menu->getOption('title'),
            $this->menu->getOption('name'),
            $this->menu->getOption('capabilities'),
            $this->menu->getIdentifier(),
            [&$this, 'callback'],
            $this->menu->getOption('icon'),
            $this->menu->getOption('position')
        );

        // Update MenuModel pages
        $this->menu->addPage($this->menu->getIdentifier(), [
            'title' => $this->menu->getOption('title'),
            'name' => $this->menu->getOption('name'),
            'sections' => $this->menu->getOption('sections'),
            'slug' => $this->menu->getIdentifier(),
        ]);

        // Add admin bar menu
        if ($this->menu->getOption('adminbar')) {
            $this->addRootAdminBar();
        }
    }

    /**
     * Add child single menu.
     *
     * @param string $slug
     * @param array $options
     * @param string $wpidentifier
     */
    public function addChild($slug, $options, $wpidentifier = '')
    {
        // Admin panel
        if (empty($slug)) {
            Notification::error(Translate::t('menu.errors.slug_is_not_defined'));

            return;
        }

        // Check page
        if ($this->menu->hasPage($slug)) {
            Notification::error(Translate::t('menu.errors.slug_is_already_used'));

            return;
        }

        // Set default option's values
        $defaults = [
            'title' => Translate::t('menu.child.defaults.title'),
            'name' => Translate::t('menu.child.defaults.name'),
            'sections' => [],
            'capabilities' => OLH_WP_CAP_MAX,
            'adminbar' => true,
        ];

        // Merge options
        $options = array_merge($defaults, $options);

        // Get odentifier
        $identifier = $this->menu->getIdentifier();
        $pageslug = !empty($identifier) ? $identifier : $wpidentifier;

        // Check page slug
        if (empty($pageslug)) {
            return;
        }

        // Add child page
        add_submenu_page(
            $pageslug,
            $options['title'],
            $options['name'],
            $options['capabilities'],
            $slug,
            [&$this, 'callback']
        );

        // Update pages
        $this->menu->addPage($slug, [
            'title' => $options['title'],
            'name' => $options['name'],
            'sections' => $options['sections'],
            'slug' => $slug,
        ]);

        // Add admin bar menu
        if ($this->menu->getOption('adminbar')) {
            $this->addChildAdminBar($slug, $options, $wpidentifier);
        }
    }

    /**
     * Add root admin bar menu.
     */
    public function addRootAdminBar()
    {
        global $wp_admin_bar;

        // Add main admin bar
        $wp_admin_bar->add_node([
            'parent' => '',
            'id' => $this->menu->getIdentifier(),
            'title' => $this->menu->getOption('title'),
            'href' => admin_url('admin.php?page='.$this->menu->getIdentifier()),
            'meta' => false
        ]);
    }

    /**
     * Define hook.
     *
     * @param string $slug
     * @param array $options
     * @param string $wpidentifier
     */
    public function addChildAdminBar($slug, $options, $wpidentifier = '')
    {
        global $wp_admin_bar;

        // Get odentifier
        $identifier = $this->menu->getIdentifier();
        $pageslug = !empty($identifier) ? $identifier : $wpidentifier;
        $pageid = !empty($identifier) ? $pageslug.'-'.$slug : $pageslug;

        // Check page slug
        if (empty($pageslug)) {
            return;
        }

        // Add child admin bar
        $wp_admin_bar->add_node([
            'parent' => $pageslug,
            'id' => $pageid,
            'title' => $options['title'],
            'href' => admin_url('admin.php?page='.$pageid),
            'meta' => false
        ]);
    }

    /**
     * Hook method.
     */
    public function callback()
    {
        // Get current page and section
        $currentPage = Request::get('page');
        $currentSection = Request::get('section');

        // Check current page
        if (!$this->menu->hasPage($currentPage)) {
            return;
        }

        // Instantiate templates
        $template = new Template();
        $template->initialize(
            $this->menu->getIdentifier(),
            $currentPage,
            $currentSection,
            $this->menu->getPage($currentPage)
        );
    }
}
