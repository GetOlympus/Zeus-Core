<?php

namespace GetOlympus\Zeus\Configuration\Configs\Cleaners;

use GetOlympus\Zeus\Utils\Helpers;

/**
 * Cleaner abstract controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Configs\Cleaners
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.49
 *
 */

abstract class Cleaner
{
    /**
     * @var string
     */
    protected $append = '';

    /**
     * @var array
     */
    protected $available = [];

    /**
     * @var string
     */
    protected $classname = '';

    /**
     * Add all usefull WP filters and hooks.
     *
     * @param  array   $args
     */
    public function init($args) : void
    {
        if (empty($args)) {
            return;
        }

        // Special case
        if (is_bool($args) && $args) {
            $args = $this->available;
        }

        // Iterate on all
        foreach ($args as $key => $status) {
            $key = strtolower($key);

            if (!in_array($key, $this->available)) {
                continue;
            }

            if (!$status) {
                continue;
            }

            $key = str_replace('_', '-', $key);
            $function = Helpers::toFunctionFormat($key);

            if (empty($this->classname)) {
                $function = strtolower(substr(strrchr(get_called_class(), "\\"), 1)).ucfirst($function).$this->append;

                // Call function
                $this->$function();
            } else {
                $function = $this->classname.ucfirst($function).$this->append;

                // Instanciate and initialize object
                new $function($status);
            }
        }
    }
}
