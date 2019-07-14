<?php

namespace GetOlympus\Zeus\Ajax\Implementation;

/**
 * Ajax implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface AjaxImplementation
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
