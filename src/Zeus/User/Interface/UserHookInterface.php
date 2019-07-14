<?php

namespace GetOlympus\Zeus\User\Interface;

/**
 * User hook interface.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserHookInterface
{
    /**
     * Hook to display user custom fields.
     *
     * @param  object  $user
     */
    public function showProfileFields($user);
}
