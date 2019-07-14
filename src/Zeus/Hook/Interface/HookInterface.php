<?php

namespace GetOlympus\Zeus\Hook\Interface;

/**
 * Hook interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Hook\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface HookInterface
{
    /**
     * Initialize all data.
     *
     * @param  string  $type
     * @param  string  $identifier
     * @param  mixed   $callback
     * @param  mixed   $priority
     */
    public function init($type, $identifier, $callback, $priority = 10);

    /**
     * Define hook.
     *
     * @param  mixed   $args
     * @return void
     */
    public function listen($args = null);

    /**
     * Execute hook action/filter.
     *
     * @return void
     */
    public function run();
}
