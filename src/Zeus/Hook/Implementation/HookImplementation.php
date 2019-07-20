<?php

namespace GetOlympus\Zeus\Hook\Implementation;

/**
 * Hook implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Hook\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface HookImplementation
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
     *
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
