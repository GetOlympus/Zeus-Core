<?php

namespace GetOlympus\Zeus\User\Controller;

/**
 * User hook interface.
 *
 * @package Olympus Zeus-Core
 * @subpackage User\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.6
 *
 */

interface UserHookInterface
{
    /**
     * Hook to display user custom fields.
     *
     * @param object $user
     */
    public function showProfileFields($user);
}
