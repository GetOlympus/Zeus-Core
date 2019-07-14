<?php

namespace GetOlympus\Zeus\User\Implementation;

/**
 * User hook implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserHookImplementation
{
    /**
     * Hook to display user custom fields.
     *
     * @param  object  $user
     */
    public function showProfileFields($user);
}
