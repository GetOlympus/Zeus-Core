<?php

namespace GetOlympus\Zeus\Ajax;

/**
 * Ajax interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface AjaxInterface
{
    /**
     * Initialization.
     */
    public function init() : void;

    /**
     * Hook callback method.
     */
    public function hookCallback() : void;
}
