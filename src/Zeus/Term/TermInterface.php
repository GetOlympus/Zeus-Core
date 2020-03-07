<?php

namespace GetOlympus\Zeus\Term;

/**
 * Term interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Term
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface TermInterface
{
    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws TermException
     */
    public function addFields($fields) : void;
}
