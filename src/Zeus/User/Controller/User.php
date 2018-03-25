<?php

namespace GetOlympus\Zeus\User\Controller;

use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\User\Controller\UserInterface;
use GetOlympus\Zeus\User\Controller\UserHook;
use GetOlympus\Zeus\User\Exception\UserException;
use GetOlympus\Zeus\User\Model\UserModel;

/**
 * Gets its own user.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

abstract class User extends Base implements UserInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize UserModel
        $this->model = new UserModel();

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Build UserModel and initialize hook.
     */
    public function init()
    {
        $fields = $this->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
        }

        // Register user
        $this->register();
    }

    /**
     * Register post types.
     */
    public function register()
    {
        // Store details
        $fields = $this->getModel()->getFields();
        $title = $this->getModel()->getTitle();

        // Works on hook
        $hook = new UserHook($title, $fields);
        $this->getModel()->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
