<?php

namespace GetOlympus\Zeus\Posttype;

use GetOlympus\Zeus\Metabox\Metabox;

/**
 * Post type model.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class PosttypeModel
{
    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    protected $args = [];

    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#labels
     */
    protected $labels = [];

    /**
     * @var array
     */
    protected $metabox = [];

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
    public function setArgs($args) : void
    {
        $this->args = $args;
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
     * Gets the value of metabox.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getMetabox($identifier = '') : array
    {
        return isset($this->metabox[$identifier]) ? $this->metabox[$identifier] : $this->metabox;
    }

    /**
     * Sets the value of metabox.
     *
     * @param  string  $identifier
     * @param  string  $title
     * @param  array   $fields
     */
    public function setMetabox($identifier, $title, $fields) : void
    {
        $this->metabox[$identifier] = Metabox::build($title, $fields);
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
}
