<?php

namespace GetOlympus\Hera\Ajax\Model;

use GetOlympus\Hera\Ajax\Model\AjaxModelInterface;

/**
 * Ajax model.
 *
 * @package Olympus Hera
 * @subpackage Ajax\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class AjaxModel implements AjaxModelInterface
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
     * @param array $args the args
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
     * @param string $handle the handle
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
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
