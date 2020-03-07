<?php

namespace GetOlympus\Zeus\Cron;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Cron\CronInterface;
use GetOlympus\Zeus\Cron\CronModel;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own cron task.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.11
 *
 */

abstract class Cron extends Base implements CronInterface
{
    /**
     * @var array
     */
    protected $available = ['hourly', 'twicedaily', 'daily'];

    /**
     * @var int
     */
    protected $interval = 60;

    /**
     * @var string
     */
    protected $schedule = '';

    /**
     * @var array
     */
    protected $traceid = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new CronModel();

        // Check schedule
        if (empty($this->schedule)) {
            return;
        }

        $this->traceid = uniqid();
        $options = [];

        if (!in_array($this->schedule, $this->available)) {
            $display  = !$this->interval ? Translate::t('cron.hourly') : $this->interval.Translate::t('cron.seconds');
            $interval = !$this->interval ? 1440 : $this->interval; // 1 hour

            $options = [
                'display'   => $display,
                'interval'  => $interval,
            ];

            unset($display);
            unset($interval);
        }

        // Update schedule
        $this->setOptions($options);
        $this->setSchedule($this->schedule);

        // Initialize
        $this->init();
    }

    /**
     * Initialization.
     */
    protected function init() : void
    {
        // Build vars
        $options = $this->getModel()->getOptions();
        $schedule = $this->getModel()->getSchedule();

        // Check velocity
        if (!empty($options)) {
            // Build new vars
            $new_evt = [
                $schedule => $options
            ];

            // Add schedule details
            add_filter('cron_schedules', function ($events) use ($new_evt) {
                return array_merge($events, $new_evt);
            });
        }

        // Set name
        $class = $this->getClass();
        $name = 'zeus_cron_'.$class['name'].'_'.$schedule;

        // Execute the right function
        if (!wp_next_scheduled($name)) {
            wp_schedule_event(time(), $schedule, $name);
        }

        // Enable action
        add_action($name, [$this, 'callback']);
    }

    /**
     * Set cron options.
     *
     * @param  array   $options
     */
    protected function setOptions($options) : void
    {
        if (empty($options)) {
            return;
        }

        $o = $this->getModel()->getOptions();
        $o = array_merge($o, $options);

        $this->getModel()->setOptions($o);
    }

    /**
     * Set cron schedule.
     *
     * @param  string  $schedule
     */
    protected function setSchedule($schedule) : void
    {
        if (empty($schedule)) {
            return;
        }

        $this->getModel()->setSchedule($schedule);
    }

    /**
     * Trace message on cron log file.
     *
     * @param  string  $message
     * @param  bool    $date
     */
    protected function trace($message, $date = false) : void
    {
        // Build vars
        $date = $date ? ' @'.date('Y-m-d H:i') : '';
        echo $this->traceid.' > '.$message.$date."\n";
    }

    /**
     * Callback custom function.
     */
    abstract public function callback() : void;
}
