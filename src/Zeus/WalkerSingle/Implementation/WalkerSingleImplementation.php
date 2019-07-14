<?php

namespace GetOlympus\Zeus\WalkerSingle\Implementation;

/**
 * Walker single implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage WalkerSingle\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.2
 *
 */

interface WalkerSingleImplementation
{
    /**
     * Starts the list before the elements are added.
     *
     * @param  string  $output
     * @param  integer $depth
     * @param  array   $args
     */
    public function start_lvl(&$output, $depth = 0, $args = []);

    /**
     * Ends the list of after the elements are added.
     *
     * @param  string  $output
     * @param  integer $depth
     * @param  array   $args
     */
    public function end_lvl(&$output, $depth = 0, $args = []);

    /**
     * Start the element output.
     *
     * @param  string  $output
     * @param  object  $category
     * @param  integer $depth
     * @param  array   $args
     * @param  integer $id
     */
    public function start_el(&$output, $category, $depth = 0, $args = [], $id = 0);

    /**
     * Ends the element output, if needed.
     *
     * @param  string  $output
     * @param  object  $category
     * @param  integer $depth
     * @param  array   $args
     */
    public function end_el(&$output, $category, $depth = 0, $args = []);
}
