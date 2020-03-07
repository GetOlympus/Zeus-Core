<?php

namespace GetOlympus\Zeus\User;

/**
 * User interface.
 *
 * @package    OlympusZeusCore
 * @subpackage User
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserInterface
{
    /**
     * Adds new fields.
     *
     * @param  array   $fields
     *
     * @throws UserException
     */
    public function addFields($fields) : void;
}
