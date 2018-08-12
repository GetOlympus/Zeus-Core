<?php

namespace GetOlympus\Zeus\Helpers\Controller;

/**
 * Helpers interface.
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

interface HelpersInterface
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
     * Helper function to create a file in a target path with its contents.
     *
     * @param string $targetFilepath
     * @param string $message
     * @param boolean $useDate
     */
    public static function filePutContents($filepath, $contents, $message, $usedate = true);

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
