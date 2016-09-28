<?php

namespace GetOlympus\Hera\Cron\Controller;

use GetOlympus\Hera\Cron\Controller\CronInterface;
use GetOlympus\Hera\Cron\Model\CronModel;
use GetOlympus\Hera\Base\Controller\Base;
use GetOlympus\Hera\Hook\Controller\Hook;

/**
 * Gets its own cron task.
 *
 * @package Olympus Hera
 * @subpackage Cron\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.11
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
        $schedule = $this->getModel()->getSchedule();
        $options = $this->getModel()->getOptions();

        // Check schedule
        if (empty($schedule)) {
            return;
        }

        // Check velocity
        if (!in_array($schedule, ['hourly', 'twicedaily', 'daily']) && !empty($options)) {
            // Build new vars
            $new_evt = [
                $key => $options
            ];

            // Add schedule details
            add_filter('cron_schedules', function ($events) use ($new_evt) {
                return array_merge($events, $new_evt);
            });
        }

        // Execute the right function
        if (!wp_next_scheduled('hera_cron_'.$schedule)) {
            wp_schedule_event(time(), $schedule, 'hera_cron_'.$schedule);
        }

        // Enable action
        add_action('hera_cron_'.$schedule, [&$this, 'cronCallback']);
    }

    /**
     * Cron hook callback method.
     */
    public function cronCallback()
    {
        // Call custom callback function
        $this->callback();
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
