<?php

namespace GetOlympus\Zeus\Walker;

use GetOlympus\Zeus\Base\BaseWalker;
use GetOlympus\Zeus\Walker\WalkerInterface;

/**
 * Gets its own walker.
 *
 * @package    OlympusZeusCore
 * @subpackage Walker
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

abstract class Walker extends BaseWalker implements WalkerInterface
{
    /**
     * Starts the list before the elements are added.
     *
     * @param string $output
     * @param int $depth
     * @param array $args
     */
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $output .= $indent."\n";

        // Customize output
        $output = $this->startLevel($output, $depth, $args);
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @param string $output
     * @param int $depth
     * @param array $args
     */
    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $output .= $indent."\n";

        // Customize output
        $output = $this->endLevel($output, $depth, $args);
    }

    /**
     * Traverse elements to create list from elements.
     *
     * @param object $element
     * @param array $children_elements
     * @param int $max_depth
     * @param int $depth
     * @param array $args
     * @param string $output
     */
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        $id_field = $this->db_fields['id'];

        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }

        // Customize element display
        $element = $this->displayElement($element, $children_elements, $max_depth, $depth, $args, $output);

        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    /**
     * Start the element output.
     *
     * @param string $output
     * @param object $item
     * @param int $depth
     * @param array $args
     * @param int $id
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $rand = bin2hex(random_bytes(10));

        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $output .= $indent;

        // Classes
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if (!$args->has_children) {
            $classes[] = 'item';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $args->has_children ? $class_names.' opener' : $class_names;
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        // Build attributes
        $attributes = '';
        $atts = [
            'href'      => !empty($item->url) ? $item->url : '',
            'title'     => !empty($item->attr_title) ? $item->attr_title : '',
            'target'    => !empty($item->target) ? $item->target : '',
            'rel'       => !empty($item->xfn) ? $item->xfn : '',
            'data-sub'  => $args->has_children ? $rand : '',
        ];

        // Iterate on all
        foreach ($atts as $attr => $value) {
            if (empty($value)) {
                continue;
            }

            $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
            $attributes .= ' '.$attr.'="'.$value.'"';
        }

        if ($args->has_children) {
            $output .= '<div class="dropdown item">';
        }

        // Build item
        $item_output = $args->before;
        $item_output .= '<a'.$attributes.$class_names.'>';
        $item_output .= $args->link_before.apply_filters('the_title', $item->title, $item->ID).$args->link_after;

        if ($args->has_children) {
            $item_output .= '<i class="dropdown icon"></i>';
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        // Build children
        if ($args->has_children) {
            $output .= '<nav id="'.$rand.'" class="m-submenu">';
        }

        $output = apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);

        // Customize output
        $output = $this->startElement($output, $item, $depth, $args, $id);
    }

    /**
     * Ends the element output, if needed.
     *
     * @param string $output
     * @param object $category
     * @param int $depth
     * @param array $args
     */
    public function end_el(&$output, $item, $depth = 0, $args = [])
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $output .= $indent;

        // Build parent
        if (!empty($item->classes) && is_array($item->classes) &&  in_array('menu-item-has-children', $item->classes)) {
            $output .= '</nav>';
            $output .= '</div>';
        }

        $output .= "\n";

        $output = apply_filters('walker_nav_menu_end_el', $output, $item, $depth, $args);

        // Customize output
        $output = $this->endElement($output, $item, $depth, $args);
    }

    /**
     * Start level.
     */
    abstract protected function startLevel($output, $depth = 0, $args = []);

    /**
     * End level.
     */
    abstract protected function endLevel($output, $depth = 0, $args = []);

    /**
     * Start element.
     */
    abstract protected function startElement($output, $item, $depth = 0, $args = [], $id = 0);

    /**
     * Display element.
     */
    abstract protected function displayElement($element, $children_elements, $max_depth, $depth, $args, $output);

    /**
     * End element.
     */
    abstract protected function endElement($output, $item, $depth = 0, $args = []);
}
