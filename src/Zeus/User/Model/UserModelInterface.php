<?php

namespace GetOlympus\Zeus\User\Model;

use GetOlympus\Zeus\User\Controller\UserHook;

/**
 * User model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserModelInterface
{
    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields();

    /**
     * Sets the value of fields.
     *
     * @param array $fields the fields
     *
     * @return self
     */
    public function setFields(array $fields = []);

    /**
     * Gets the value of hook.
     *
     * @return UserHook
     */
    public function getHook();

    /**
     * Sets the value of hook.
     *
     * @param UserHook $hook the hook
     *
     * @return self
     */
    public function setHook(UserHook $hook);

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title);
}
