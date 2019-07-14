<?php

namespace GetOlympus\Zeus\Cron\Controller;

use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Cron\Interface\CronInterface;
use GetOlympus\Zeus\Cron\Model\CronModel;

/**
 * Gets its own cron task.
 *
 * @package    OlympusZeusCore
 * @subpackage Cron\Controller
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
     * @var integer
     */
    protected $interval = 60;

    /**
     * @var string
     */
    protected $schedule = '';

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
        $this->getModel()->setOptions($options);
        $this->getModel()->setSchedule($this->schedule);

        // Initialize
        $this->init();
    }

    /**
     * Initialization.
     */
    public function init()
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
        add_action($name, [&$this, 'callback']);
    }

    /**
     * Callback custom function.
     */
    abstract public function callback();
}
