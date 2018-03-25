<?php

namespace GetOlympus\Zeus\Posttype\Model;

use GetOlympus\Zeus\Posttype\Controller\PosttypeHook;

/**
 * Post type model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface PosttypeModelInterface
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
    public function setArgs($args);

    /**
     * Gets the value of hook.
     *
     * @return PosttypeHook
     */
    public function getHook();

    /**
     * Sets the value of hook.
     *
     * @param PosttypeHook $hook the hook
     *
     * @return self
     */
    public function setHook(PosttypeHook $hook);

    /**
     * Gets the value of metaboxes.
     *
     * @return array
     */
    public function getMetaboxes();

    /**
     * Sets the value of metaboxes.
     *
     * @param array $metaboxes the metaboxes
     *
     * @return self
     */
    public function setMetaboxes($metaboxes = []);

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
