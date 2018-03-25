<?php

namespace GetOlympus\Zeus\Common\Controller;

/**
 * Common interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Common\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

interface CommonInterface
{
    /**
     * Get singleton.
     */
    public static function getInstance();

    /**
     * Copy a file contents from this internal assets folder to the public dist Olympus assets folder.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @param string $filename
     */
    public function copyFile($sourcePath, $targetPath, $filename);

    /**
     * Camelize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $camelized
     */
    public static function toCamelCaseFormat($text, $separator = '-');

    /**
     * Functionize string.
     *
     * @param string $text
     * @param string $separator
     * @return string $functionized
     */
    public static function toFunctionFormat($text, $separator = '-');

    /**
     * Slugify string.
     *
     * @param string $text
     * @param string $separator
     * @return string $slugified
     */
    public static function urlize($text, $separator = '-');
}
