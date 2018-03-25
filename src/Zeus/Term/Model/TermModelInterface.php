<?php

namespace GetOlympus\Zeus\Term\Model;

use GetOlympus\Zeus\Term\Controller\TermHook;

/**
 * Term model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Term\Model
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
     * @param array $args the args
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
     * @param array $fields the fields
     *
     * @return self
     */
    public function setFields(array $fields = []);

    /**
     * Gets the value of hook.
     *
     * @return TermHook
     */
    public function getHook();

    /**
     * Sets the value of hook.
     *
     * @param TermHook $hook the hook
     *
     * @return self
     */
    public function setHook(TermHook $hook);

    /**
     * Gets the value of posttype.
     *
     * @return string
     */
    public function getPosttype();

    /**
     * Sets the value of posttype.
     *
     * @param string $posttype the posttype
     *
     * @return self
     */
    public function setPosttype($posttype);

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Sets the value of slug.
     *
     * @param string $slug the slug
     *
     * @return self
     */
    public function setSlug($slug);
}
