<?php

namespace GetOlympus\Zeus\Term\Interface;

/**
 * Term model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Term\Interface
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface TermModelInterface
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
    public function setFields(array $fields = []);

    /**
     * Gets the value of labels.
     *
     * @return array
     */
    public function getLabels();

    /**
     * Sets the value of labels.
     *
     * @param  array   $labels
     *
     * @return self
     */
    public function setLabels(array $labels);

    /**
     * Gets the value of posttype.
     *
     * @return string
     */
    public function getPosttype();

    /**
     * Sets the value of posttype.
     *
     * @param  string  $posttype
     *
     * @return self
     */
    public function setPosttype(string $posttype);

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
    public function setSlug(string $slug);
}
