<?php

namespace GetOlympus\Hera\Render\Controller;

/**
 * Render interface.
 *
 * @package Olympus Hera
 * @subpackage Render\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.2
 *
 */

interface RenderInterface
{
    /**
     * Get singleton.
     */
    public static function getInstance();

    /**
     * Render assets on asked page.
     *
     * @param array $currentPage
     * @param array $fields
     */
    public static function assets($currentPage, $fields);

    /**
     * Create temporary asset accessible file.
     *
     * @param string $source
     * @param string $filename
     */
    public static function assetsInCache($source, $filename);

    /**
     * Camelize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $camelized
     */
    public static function camelCase($text, $separator = '-');

    /**
     * Functionize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $functionized
     */
    public static function toFunction($text, $separator = '-');

    /**
     * Slugify string.
     *
     * @param string $text
     * @param string $separator
     * @return string $slugified
     */
    public static function urlize($text, $separator = '-');

    /**
     * Render TWIG component.
     *
     * @param string $template
     * @param array $vars
     * @param string $context
     */
    public static function view($template, $vars, $context = 'core');
}
