<?php

namespace GetOlympus\Zeus\Helpers\Implementation;

/**
 * Helpers implementation.
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Implementation
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

interface HelpersImplementation
{
    /**
     * Get singleton.
     */
    public static function getInstance();

    /**
     * Copy a file contents from this internal assets folder to the public dist Olympus assets folder.
     *
     * @param  string  $sourcePath
     * @param  string  $targetPath
     * @param  string  $filename
     */
    public static function copyFile($sourcePath, $targetPath, $filename);

    /**
     * Helper function to create a file in a target path with its contents.
     *
     * @param  string  $filepath
     * @param  string  $contents
     * @param  string  $message
     * @param  boolean $usedate
     */
    public static function filePutContents($filepath, $contents, $message, $usedate = true);

    /**
     * Camelize string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function toCamelCaseFormat($text, $separator = '-');

    /**
     * Functionize string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function toFunctionFormat($text, $separator = '-');

    /**
     * Slugify string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function urlize($text, $separator = '-');
}
