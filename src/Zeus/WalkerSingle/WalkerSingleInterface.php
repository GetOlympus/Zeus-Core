<?php

namespace GetOlympus\Zeus\WalkerSingle;

/**
 * Walker single interface.
 *
 * @package    OlympusZeusCore
 * @subpackage WalkerSingle
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface WalkerSingleInterface
{
    /**
     * Starts the list before the elements are added.
     *
     * @param  string  $output
     * @param  int     $depth
     * @param  array   $args
     */
    public function start_lvl(&$output, $depth = 0, $args = []); // phpcs:ignore

    /**
     * Ends the list of after the elements are added.
     *
     * @param  string  $output
     * @param  int     $depth
     * @param  array   $args
     */
    public function end_lvl(&$output, $depth = 0, $args = []); // phpcs:ignore

    /**
     * Start the element output.
     *
     * @param  string  $output
     * @param  object  $category
     * @param  int     $depth
     * @param  array   $args
     * @param  int     $id
     */
    public function start_el(&$output, $category, $depth = 0, $args = [], $id = 0); // phpcs:ignore

    /**
     * Ends the element output, if needed.
     *
     * @param  string  $output
     * @param  object  $category
     * @param  int     $depth
     * @param  array   $args
     */
    public function end_el(&$output, $category, $depth = 0, $args = []); // phpcs:ignore
}
