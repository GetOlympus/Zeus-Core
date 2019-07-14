<?php

namespace GetOlympus\Zeus\User\Implementation;

/**
 * User implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserImplementation
{
    /**
     * Build TermModel and initialize hook.
     */
    public function init();

    /**
     * Register post types.
     */
    public function register();
}
