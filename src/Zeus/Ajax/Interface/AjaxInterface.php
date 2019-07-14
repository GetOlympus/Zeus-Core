<?php

namespace GetOlympus\Zeus\Ajax\Interface;

/**
 * Ajax interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface AjaxInterface
{
    /**
     * Initialization.
     */
    public function init();

    /**
     * Hooks and enqueue script.
     *
     * @param array $options the options
     */
    public function enqueueScript($options);

    /**
     * Hook callback method.
     */
    public function hookCallback();
}
