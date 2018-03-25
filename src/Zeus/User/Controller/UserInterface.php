<?php

namespace GetOlympus\Zeus\User\Controller;

/**
 * User interface.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserInterface
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
