<?php

namespace GetOlympus\Zeus\Ajax\Implementation;

/**
 * Ajax model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Ajax\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.3
 *
 */

interface AjaxModelImplementation
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
     * Gets the value of handle.
     *
     * @return string
     */
    public function getHandle();

    /**
     * Sets the value of handle.
     *
     * @param string $handle the handle
     *
     * @return self
     */
    public function setHandle($handle);

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name);
}
