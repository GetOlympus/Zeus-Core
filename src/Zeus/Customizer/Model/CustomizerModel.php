<?php

namespace GetOlympus\Zeus\Customizer\Model;

use GetOlympus\Zeus\Customizer\Implementation\CustomizerModelImplementation;

/**
 * Customizer model.
 *
 * @package    OlympusZeusCore
 * @subpackage Customizer\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

class CustomizerModel implements CustomizerModelImplementation
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
    public function getControls($identifier = '')
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
     *
     * @return self
     */
    public function setControls($identifier, $options)
    {
        $this->controls[$identifier] = $options;

        return $this;
    }

    /**
     * Gets the value of panels.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getPanels($identifier = '')
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
     *
     * @return self
     */
    public function setPanels($identifier, $options)
    {
        $this->panels[$identifier] = $options;

        return $this;
    }

    /**
     * Gets the value of sections.
     *
     * @param  string  $identifier
     *
     * @return array
     */
    public function getSections($identifier = '')
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
     *
     * @return self
     */
    public function setSections($identifier, $options)
    {
        $this->sections[$identifier] = $options;

        return $this;
    }
}
