<?php

namespace GetOlympus\Hera\User\Controller;

/**
 * User interface.
 *
 * @package Olympus Hera
 * @subpackage User\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.6
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
