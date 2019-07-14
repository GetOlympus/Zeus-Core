<?php

namespace GetOlympus\Zeus\User\Interface;

/**
 * User interface.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Interface
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
