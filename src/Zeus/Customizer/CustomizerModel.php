<?php

namespace GetOlympus\Zeus\Customizer;

/**
 * Customizer model.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

class CustomizerModel
{
    /**
     * @var array
     */
    protected $controls = [];

    /**
     * @var array
     */
    protected $panels = [];

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * Gets the value of controls.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getControls($identifier = '') : array
    {
        if (!empty($identifier)) {
            return isset($this->controls[$identifier]) ? $this->controls[$identifier] : [];
        }

        return $this->controls;
    }

    /**
     * Sets the value of controls.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function setControls($identifier, $options) : void
    {
        $this->controls[$identifier] = $options;
    }

    /**
     * Gets the value of panels.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getPanels($identifier = '') : array
    {
        if (!empty($identifier)) {
            return isset($this->panels[$identifier]) ? $this->panels[$identifier] : [];
        }

        return $this->panels;
    }

    /**
     * Sets the value of panels.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function setPanels($identifier, $options) : void
    {
        $this->panels[$identifier] = $options;
    }

    /**
     * Gets the value of sections.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getSections($identifier = '') : array
    {
        if (!empty($identifier)) {
            return isset($this->sections[$identifier]) ? $this->sections[$identifier] : [];
        }

        return $this->sections;
    }

    /**
     * Sets the value of sections.
     *
     * @param  string  $identifier
     * @param  array   $options
     */
    public function setSections($identifier, $options) : void
    {
        $this->sections[$identifier] = $options;
    }
}
