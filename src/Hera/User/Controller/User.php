<?php

namespace GetOlympus\Hera\User\Controller;

use GetOlympus\Hera\User\Controller\UserInterface;
use GetOlympus\Hera\User\Exception\UserException;
use GetOlympus\Hera\User\Model\UserModel;

/**
 * Gets its own user.
 *
 * @package Olympus Hera
 * @subpackage User\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.6
 *
 */

abstract class User implements UserInterface
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var UserModel
     */
    protected $user;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Build TermModel and initialize hook.
     */
    public function init()
    {
        // Check fields
        if (empty($this->fields)) {
            return;
        }

        // Initialize UserModel
        $this->user = new UserModel();
        $this->user->setFields($this->fields);
        $this->user->setTitle($this->title);

        // Register user
        $this->register();
    }

    /**
     * Register post types.
     */
    public function register()
    {
        // Store details
        $fields = $this->user->getFields();
        $title = $this->user->getTitle();

        // Works on hook
        $hook = new UserHook($title, $fields);
        $this->user->setHook($hook);
    }

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
