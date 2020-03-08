<?php

namespace GetOlympus\Zeus\Term;

/**
 * Term model.
 *
 * @package    OlympusZeusCore
 * @subpackage Term
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class TermModel
{
    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     */
    protected $labels = [];

    /**
     * @var string
     */
    protected $posttype = '';

    /**
     * @var string
     */
    protected $slug = '';

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
     * Gets the value of labels.
     *
     * @return array
     */
    public function getLabels() : array
    {
        return $this->labels;
    }

    /**
     * Sets the value of labels.
     *
     * @param  array   $labels
     */
    public function setLabels(array $labels) : void
    {
        $this->labels = $labels;
    }

    /**
     * Gets the value of posttype.
     *
     * @return string
     */
    public function getPosttype() : string
    {
        return $this->posttype;
    }

    /**
     * Sets the value of posttype.
     *
     * @param  string  $posttype
     */
    public function setPosttype(string $posttype) : void
    {
        $this->posttype = $posttype;
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
    public function setSlug(string $slug) : void
    {
        $this->slug = $slug;
    }
}
