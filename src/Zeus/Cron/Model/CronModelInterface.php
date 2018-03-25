<?php

namespace GetOlympus\Zeus\Cron\Model;

/**
 * Cron model interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

interface CronModelInterface
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
     * @param array $options the options
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
     * @param string $schedule the schedule
     *
     * @return self
     */
    public function setSchedule($schedule);
}
