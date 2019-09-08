<?php

namespace GetOlympus\Zeus\User\Implementation;

/**
 * User model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

interface UserModelImplementation
{
    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields();

    /**
     * Sets the value of fields.
     *
     * @param  array   $fields
     *
     * @return self
     */
    public function setFields(array $fields = []);

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the value of title.
     *
     * @param  string  $title
     *
     * @return self
     */
    public function setTitle($title);
}
