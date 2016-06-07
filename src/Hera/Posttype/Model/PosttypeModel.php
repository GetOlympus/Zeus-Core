<?php

namespace GetOlympus\Hera\Posttype\Model;

use GetOlympus\Hera\Posttype\Controller\PosttypeHook;

/**
 * Post type model.
 *
 * @package Olympus Hera
 * @subpackage Posttype\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class PosttypeModel
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var PosttypeHook
     */
    protected $hook;

    /**
     * @var string
     */
    protected $slug;

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
     * @param array $fields the fields
     *
     * @return self
     */
    public function setFields($fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Gets the value of hook.
     *
     * @return PosttypeHook
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Sets the value of hook.
     *
     * @param PosttypeHook $hook the hook
     *
     * @return self
     */
    public function setHook(PosttypeHook $hook)
    {
        $this->hook = $hook;

        return $this;
    }

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param string $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
