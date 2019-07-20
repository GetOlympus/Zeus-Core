<?php

namespace GetOlympus\Zeus\Ajax\Model;

use GetOlympus\Zeus\Ajax\Implementation\AjaxModelImplementation;

/**
 * Ajax model.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class AjaxModel implements AjaxModelImplementation
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string
     */
    protected $name;

    /**
     * Gets the value of args.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Sets the value of args.
     *
     * @param  array   $args
     *
     * @return self
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Gets the value of handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Sets the value of handle.
     *
     * @param  string  $handle
     *
     * @return self
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param  string  $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
