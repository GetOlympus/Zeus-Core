<?php

namespace GetOlympus\Zeus\AdminPage\Controller;

use GetOlympus\Zeus\AdminPage\Controller\AdminPageHook;
use GetOlympus\Zeus\AdminPage\Controller\AdminPageInterface;
use GetOlympus\Zeus\AdminPage\Exception\AdminPageException;
use GetOlympus\Zeus\AdminPage\Model\AdminPageModel;
use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Gets its own admin page.
 *
 * @package    OlympusZeusCore
 * @subpackage AdminPage\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.7
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

        // Work on admin only
        if (OL_ZEUS_ISADMIN) {
            // Initialize variables and filters
            $this->setVars();
            //$this->setFilters();

            $this->init();
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

        // Set the internal pointer of an array to its first element
        reset($pages);

        // Update identifier
        $identifier = $this->getModel()->getIdentifier();
        $identifier = Helpers::urlize($identifier);
        $this->getModel()->setIdentifier($identifier);

        // Get parent
        $parent = $this->getModel()->getParent();

        // Add root single menu if identifier is unknown
        if (!isset($admin_page_hooks[$identifier]) && empty($parent)) {
            $this->addRootPage();
        }

        // Iterate on pages
        foreach ($pages as $slug => $options) {
            if (empty($slug) || empty($options)) {
                continue;
            }

            // Get slug
            $slug = Helpers::urlize($slug);

            // Add child menu
            $this->addChild($slug, $options);
        }
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
            'capabilities'  => OL_ZEUS_WP_CAP,
            'adminbar'      => true,
            'submit'        => true,
        ];

        // Update pages
        $options = $this->getModel()->getPages($identifier);

        // Special case with name
        $options['name'] = !isset($options['name']) ? $options['title'] : $options['name'];

        // Merge values
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
        $parent = $this->getModel()->getParent();

        // Special case with name
        $options['name'] = !isset($options['name']) ? $options['title'] : $options['name'];

        // Check slug
        if (empty($slug)) {
            throw new AdminPageException(Translate::t('adminpage.errors.slug_is_not_defined'));
        }

        global $admin_page_hooks;

        // Set default option's values
        $defaults = [
            'slug'          => $slug,

            // Page options
            'title'         => Translate::t('adminpage.child.defaults.title'),
            'name'          => Translate::t('adminpage.child.defaults.name'),
            'description'   => '',
            'sections'      => [],

            // Options
            'capabilities'  => OL_ZEUS_WP_CAP,
            'adminbar'      => true,
            'submit'        => true,
        ];

        // Merge options
        $options = array_merge($defaults, $options);
        $this->getModel()->updatePage($slug, $options);

        $pageid = $slug === $identifier ? $identifier : $identifier.'-'.$slug;

        // Add child page
        if (!empty($parent) && isset($admin_page_hooks[$parent])) {
            $function = 'add_options_page';

            if ('index.php' === $parent) {
                $function = 'add_dashboard_page';
            } else if ('upload.php' === $parent) {
                $function = 'add_media_page';
            } else if ('link-manager.php' === $parent) {
                $function = 'add_links_page';
            } else if ('edit-comments.php' === $parent) {
                $function = 'add_comments_page';
            } else if ('edit.php' === $parent) {
                $function = 'add_posts_page';
            } else if ('edit.php?post_type=page' === $parent) {
                $function = 'add_pages_page';
            } else if ('themes.php' === $parent) {
                $function = 'add_theme_page';
            } else if ('plugins.php' === $parent) {
                $function = 'add_plugins_page';
            } else if ('users.php' === $parent) {
                $function = 'add_users_page';
            } else if ('tools.php' === $parent) {
                $function = 'add_management_page';
            }

            // Add custom admin page
            $function(
                $options['title'],
                $options['name'],
                $options['capabilities'],
                $pageid,
                [&$this, 'callback']
            );
        } else {
            add_submenu_page(
                $identifier,
                $options['title'],
                $options['name'],
                $options['capabilities'],
                $pageid,
                [&$this, 'callback']
            );

            // Update parent to empty
            $this->getModel()->setParent('');
        }

        // Add admin bar menu
        if ($options['adminbar'] && empty($parent)) {
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
        $parent = $this->getModel()->getParent();
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
        $hook = new AdminPageHook($currentPage, $currentSection, $parent, $options);
        $this->getModel()->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
