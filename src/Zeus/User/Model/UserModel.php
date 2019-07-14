<?php

namespace GetOlympus\Zeus\User\Model;

use GetOlympus\Zeus\User\Interface\UserModelInterface;

/**
 * User model.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

class UserModel implements UserModelInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var string
     */
    protected $title;

    /**
     * Gets the value of fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sets the value of fields.
     *
     * @param  array   $fields
     *
     * @return self
     */
    public function setFields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param  string  $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
