<?php

namespace GetOlympus\Zeus\Widget;

/**
 * Widget interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Widget
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface WidgetInterface
{
    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws WidgetException
     */
    public function addFields($fields) : void;
}
