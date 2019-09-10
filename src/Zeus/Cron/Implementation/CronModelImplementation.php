<?php

namespace GetOlympus\Zeus\Cron\Implementation;

/**
 * Cron model implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

interface CronModelImplementation
{
    /**
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Sets the value of options.
     *
     * @param  array   $options
     *
     * @return self
     */
    public function setOptions($options);

    /**
     * Gets the value of schedule.
     *
     * @return string
     */
    public function getSchedule();

    /**
     * Sets the value of schedule.
     *
     * @param  string  $schedule
     *
     * @return self
     */
    public function setSchedule($schedule);
}
