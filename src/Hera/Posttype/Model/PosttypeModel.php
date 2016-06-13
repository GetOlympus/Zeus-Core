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
     * @var PosttypeHook
     */
    protected $hook;

    /**
     * @var array
     */
    protected $metaboxes;

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
     * Gets the value of metaboxes.
     *
     * @return array
     */
    public function getMetaboxes()
    {
        return $this->metaboxes;
    }

    /**
     * Sets the value of metaboxes.
     *
     * @param array $metaboxes the metaboxes
     *
     * @return self
     */
    public function setMetaboxes($metaboxes = [])
    {
        $this->metaboxes = $metaboxes;

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
