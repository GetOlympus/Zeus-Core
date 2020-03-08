<?php

namespace GetOlympus\Zeus\Metabox;

/**
 * Metabox model.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class MetaboxModel
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $context = 'normal';

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $priority = 'low';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * @var string
     */
    protected $title = '';

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
    public function setArgs(array $args) : void
    {
        $this->args = $args;
    }

    /**
     * Gets the value of callback.
     *
     * @return callable
     */
    public function getCallback() : callable
    {
        return $this->callback;
    }

    /**
     * Sets the value of callback.
     *
     * @param  callable $callback
     */
    public function setCallback(callable $callback) : void
    {
        $this->callback = $callback;
    }

    /**
     * Gets the value of context.
     *
     * @return string
     */
    public function getContext() : string
    {
        return $this->context;
    }

    /**
     * Sets the value of context.
     *
     * @param  string  $context
     */
    public function setContext(string $context) : void
    {
        $this->context = $context;
    }

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
    public function setFields(array $fields) : void
    {
        $this->fields = $fields;
    }

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param  string  $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * Gets the value of priority.
     *
     * @return string
     */
    public function getPriority() : string
    {
        return $this->priority;
    }

    /**
     * Sets the value of priority.
     *
     * @param  string  $priority
     */
    public function setPriority(string $priority) : void
    {
        $this->priority = $priority;
    }

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param  string  $slug
     */
    public function setSlug($slug) : void
    {
        $this->slug = $slug;
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
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }
}
