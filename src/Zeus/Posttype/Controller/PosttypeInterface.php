<?php

namespace GetOlympus\Zeus\Posttype\Controller;

/**
 * Posttype interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Posttype\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface PosttypeInterface
{
    /**
     * Build PosttypeModel and initialize hook.
     */
    public function init();

    /**
     * Build args.
     *
     * @return array $args
     */
    public function defaultArgs();

    /**
     * Build labels.
     *
     * @param string $plural
     * @param string $singular
     * @return array $labels
     */
    public function defaultLabels($plural, $singular);

    /**
     * Build statuses.
     *
     * @param string $name
     * @return array $statuses
     */
    public function defaultStatuses($name);

    /**
     * Register post types.
     */
    public function register();
}
