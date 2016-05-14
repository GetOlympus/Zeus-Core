<?php

namespace GetOlympus\Hera\Metabox\Model;

/**
 * Abstract class to define Metabox model.
 *
 * @package Olympus Hera
 * @subpackage Metabox\Model
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Metabox
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var function
     */
    protected $callback;

    /**
     * @var string
     */
    protected $context = 'normal';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $priority = 'low';

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

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
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Gets the value of callback.
     *
     * @return function
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the value of callback.
     *
     * @param function $callback the callback
     *
     * @return self
     */
    public function setCallback(function $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Gets the value of context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the value of context.
     *
     * @param string $context the context
     *
     * @return self
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param string $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of priority.
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the value of priority.
     *
     * @param string $priority the priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

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
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}