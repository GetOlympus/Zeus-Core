<?php

namespace GetOlympus\Zeus\Cron;

/**
 * Cron model.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

class CronModel
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $schedule = '';

    /**
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param  array   $options
     */
    public function setOptions($options) : void
    {
        $this->options = $options;
    }

    /**
     * Gets the value of schedule.
     *
     * @return string
     */
    public function getSchedule() : string
    {
        return $this->schedule;
    }

    /**
     * Sets the value of schedule.
     *
     * @param  string  $schedule
     */
    public function setSchedule($schedule) : void
    {
        $this->schedule = $schedule;
    }
}
