<?php

namespace GetOlympus\Zeus\User;

/**
 * User model.
 *
 * @package    OlympusZeusCore
 * @subpackage User
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

class UserModel
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $title = '';

    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * Sets the value of fields.
     *
     * @param  array   $fields
     */
    public function setFields(array $fields = []) : void
    {
        $this->fields = $fields;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param  string  $title
     */
    public function setTitle($title) : void
    {
        $this->title = $title;
    }
}
