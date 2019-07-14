<?php

namespace GetOlympus\Zeus\Posttype\Model;

use GetOlympus\Zeus\Metabox\Controller\Metabox;
use GetOlympus\Zeus\Posttype\Implementation\PosttypeModelImplementation;

/**
 * Post type model.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class PosttypeModel implements PosttypeModelImplementation
{
    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     */
    protected $args;

    /**
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#labels
     */
    protected $labels;

    /**
     * @var array
     */
    protected $metabox;

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
     * Gets the value of labels.
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Sets the value of labels.
     *
     * @param  array   $labels
     *
     * @return self
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Gets the value of metabox.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getMetabox($identifier = '')
    {
        return isset($this->metabox[$identifier]) ? $this->metabox[$identifier] : $this->metabox;
    }

    /**
     * Sets the value of metabox.
     *
     * @param  string  $identifier
     * @param  string  $title
     * @param  array   $fields
     *
     * @return self
     */
    public function setMetabox($identifier, $title, $fields)
    {
        $this->metabox[$identifier] = Metabox::build($title, $fields);

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
     * @param  string  $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
