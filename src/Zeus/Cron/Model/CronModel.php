<?php

namespace GetOlympus\Zeus\Cron\Model;

use GetOlympus\Zeus\Cron\Interface\CronModelInterface;

/**
 * Cron model.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron\Model
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

class CronModel implements CronModelInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $schedule;

    /**
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param  array   $options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Sets the value of schedule.
     *
     * @param  string  $schedule
     * @param  array   $options
     *
     * @return self
     */
    public function setSchedule($schedule, $options = [])
    {
        $this->schedule = $schedule;
        $this->setOptions($options);

        return $this;
    }
}
