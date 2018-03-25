<?php

namespace GetOlympus\Zeus\Cron\Controller;

use GetOlympus\Zeus\Cron\Controller\CronInterface;
use GetOlympus\Zeus\Cron\Model\CronModel;
use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Hook\Controller\Hook;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new CronModel();

        // Initialize
        $this->setVars();
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

        // Check schedule
        if (empty($schedule)) {
            return;
        }

        // Check velocity
        if (!in_array($schedule, ['hourly', 'twicedaily', 'daily']) && !empty($options)) {
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

    /**
     * Prepare variables.
     */
    abstract public function setVars();
}
