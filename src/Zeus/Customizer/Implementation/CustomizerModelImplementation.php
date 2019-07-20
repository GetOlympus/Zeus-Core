<?php

namespace GetOlympus\Zeus\Customizer\Implementation;

/**
 * Customizer model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

interface CustomizerModelImplementation
{
    /**
     * Gets the value of controls.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getControls($identifier = '');

    /**
     * Sets the value of controls.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @return self
     */
    public function setControls($identifier, $options);

    /**
     * Gets the value of panels.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getPanels($identifier = '');

    /**
     * Sets the value of panels.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @return self
     */
    public function setPanels($identifier, $options);

    /**
     * Gets the value of sections.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getSections($identifier = '');

    /**
     * Sets the value of sections.
     *
     * @param  string  $identifier
     * @param  array   $options
     *
     * @return self
     */
    public function setSections($identifier, $options);
}
