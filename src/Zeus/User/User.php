<?php

namespace GetOlympus\Zeus\User;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\User\UserHook;
use GetOlympus\Zeus\User\UserException;
use GetOlympus\Zeus\User\UserInterface;
use GetOlympus\Zeus\User\UserModel;

/**
 * Gets its own user.
 *
 * @package    OlympusZeusCore
 * @subpackage User
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

abstract class User extends Base implements UserInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Initialize UserModel
        $this->model = new UserModel();

        // Update model
        $this->setTitle($this->title);

        // Initialize
        $this->setVars();
        $this->init();
    }

    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws UserException
     */
    public function addFields($fields) : void
    {
        // Check fields
        if (empty($fields)) {
            throw new UserException(Translate::t('user.errors.fields_are_not_defined'));
        }

        // Update fields
        $this->getModel()->setFields($fields);
    }

    /**
     * Build UserModel and initialize hook.
     */
    protected function init() : void
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
    protected function register() : void
    {
        // Works on hook
        new UserHook($this);
    }

    /**
     * Set title.
     *
     * @param  string  $title
     */
    protected function setTitle($title) : void
    {
        if (empty($title)) {
            return;
        }

        $this->getModel()->setTitle($title);
    }

    /**
     * Prepare variables.
     */
    abstract protected function setVars() : void;
}
