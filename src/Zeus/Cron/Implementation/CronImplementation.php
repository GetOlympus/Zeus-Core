<?php

namespace GetOlympus\Zeus\Cron\Implementation;

/**
 * Cron implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

interface CronImplementation
{
    /**
     * Initialization.
     */
    public function init();

    /**
     * Trace message on cron log file.
     *
     * @param  string  $message
     * @param  boolean $date
     */
    public function trace($message, $date = false);
}
