<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use GetOlympus\Zeus\Helpers\Implementation\HelpersImplementation;
use Behat\Transliterator\Transliterator;

/**
 * Helpers controller
 *
 * @package    OlympusZeusCore
 * @subpackage Helpers\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

class Helpers implements HelpersImplementation
{
    /**
     * @var Singleton
     */
    private static $instance;

    /**
     * Get singleton.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Copy a file contents from this internal assets folder to the public dist Olympus assets folder.
     *
     * @param  string  $sourcePath
     * @param  string  $targetPath
     * @param  string  $filename
     * @param  boolean $symlink
     */
    public static function copyFile($sourcePath, $targetPath, $filename, $symlink = false)
    {
        // Check paths
        if ($sourcePath === $targetPath) {
            return;
        }

        $targetFilepath = rtrim($targetPath, S).S.$filename;

        // Check if file exists and create it
        if (file_exists($targetFilepath)) {
            return;
        }

        // Build new contents
        $sourceFilepath = rtrim($sourcePath, S).S.$filename;

        // Set symlink
        $symlink = OL_ZEUS_USECACHE ? true : $symlink;

        // Check the old file to copy its contents
        if (file_exists($sourceFilepath)) {
            $copy = $symlink ? symlink($sourceFilepath, $targetFilepath) : copy($sourceFilepath, $targetFilepath);
        } else {
            self::filePutContents(
                $targetFilepath,
                '',
                'This file has been auto-generated by the Zeus package without any content'
            );
        }
    }

    /**
     * Helper function to create a file in a target path with its contents.
     *
     * @param  string  $filepath
     * @param  string  $contents
     * @param  string  $message
     * @param  boolean $usedate
     */
    public static function filePutContents($filepath, $contents, $message, $usedate = true)
    {
        $suffix = '';

        // Check date
        if ($usedate) {
            $suffix = ' on '.date('l jS \of F Y h:i:s A');
        }

        // Update contents
        $contents = !empty($contents) ? $contents."\n" : $contents;

        // Copy file contents
        file_put_contents($filepath, "/**\n * ".$message.$suffix.".\n */\n\n".$contents);
    }

    /**
     * Camelize string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function toCamelCaseFormat($text, $separator = '-')
    {
        $slugified = self::urlize($text, $separator);
        $camel = strtolower($slugified);
        $camel = ucwords($camel, $separator);

        return str_replace($separator, '', $camel);
    }

    /**
     * Functionize string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function toFunctionFormat($text, $separator = '-')
    {
        $camelized = self::toCamelCaseFormat($text, $separator);

        return lcfirst($camelized);
    }

    /**
     * Slugify string.
     *
     * @param  string  $text
     * @param  string  $separator
     *
     * @return string
     */
    public static function urlize($text, $separator = '-')
    {
        return Transliterator::urlize($text, $separator);
    }
}
