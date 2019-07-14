<?php

namespace GetOlympus\Zeus\Metabox\Implementation;

/**
 * Metabox model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface MetaboxModelImplementation
{
    /**
     * Gets the value of args.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Sets the value of args.
     *
     * @param  array   $args
     *
     * @return self
     */
    public function setArgs(array $args);

    /**
     * Gets the value of callback.
     *
     * @return function
     */
    public function getCallback();

    /**
     * Sets the value of callback.
     *
     * @param  function $callback
     *
     * @return self
     */
    public function setCallback($callback);

    /**
     * Gets the value of context.
     *
     * @return string
     */
    public function getContext();

    /**
     * Sets the value of context.
     *
     * @param  string  $context
     *
     * @return self
     */
    public function setContext($context);

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
    public function setFields(array $fields);

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the value of id.
     *
     * @param  string  $id
     *
     * @return self
     */
    public function setId($id);

    /**
     * Gets the value of priority.
     *
     * @return string
     */
    public function getPriority();

    /**
     * Sets the value of priority.
     *
     * @param  string  $priority
     *
     * @return self
     */
    public function setPriority($priority);

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Sets the value of slug.
     *
     * @param  string  $slug
     *
     * @return self
     */
    public function setSlug($slug);

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
