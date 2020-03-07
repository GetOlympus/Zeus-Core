<?php

namespace GetOlympus\Zeus\Ajax;

/**
 * Ajax model.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class AjaxModel
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var string
     */
    protected $handle = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * Gets the value of args.
     *
     * @return array
     */
    public function getArgs() : array
    {
        return $this->args;
    }

    /**
     * Sets the value of args.
     *
     * @param  array   $args
     */
    public function setArgs($args) : void
    {
        $this->args = $args;
    }

    /**
     * Gets the value of handle.
     *
     * @return string
     */
    public function getHandle() : string
    {
        return $this->handle;
    }

    /**
     * Sets the value of handle.
     *
     * @param  string  $handle
     */
    public function setHandle($handle) : void
    {
        $this->handle = $handle;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param  string  $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }
}
