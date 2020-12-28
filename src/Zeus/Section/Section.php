<?php

namespace GetOlympus\Zeus\Section;

use GetOlympus\Zeus\Base\BaseSection;
use GetOlympus\Zeus\Section\SectionException;
use GetOlympus\Zeus\Utils\Helpers;

/**
 * Abstract class to define all Section context with authorized sections, how to
 * write some functions and every usefull checks.
 *
 * @package    OlympusZeusCore
 * @subpackage Section
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.1.8
 *
 */

abstract class Section extends BaseSection
{
    /**
     * @var array
     */
    public static $scripts = [];

    /**
     * @var array
     */
    public static $styles = [];

    /**
     * @var string
     */
    protected $textdomain = 'zeussection';

    /**
     * @var string
     */
    public $type = 'zeus-section';

    /**
     * Enqueue scripts and styles.
     */
    public static function assets() : void
    {
        // Get instance
        try {
            $section = self::getInstance();
        } catch (Exception $e) {
            throw new SectionException(Translate::t('section.errors.class_is_not_defined'));
        }

        $script = <<<EOT
<script type="text/javascript">(function($,api){
    api.sectionConstructor['$section->type']=api.Section.extend({
        attachEvents:function(){},isContextuallyActive:function(){return true;}
    });
})(jQuery,wp.customize);</script>
EOT;

        $defaults = [
            'dependency' => 'customize-controls',
            'src'        => '',
            'deps'       => [],
            'ver'        => false,
            'in_footer'  => true,
            'media'      => 'all',
        ];

        foreach (['js' => static::$scripts, 'css' => static::$styles] as $type => $files) {
            if (empty($files)) {
                if ('js' === $type) {
                    add_action('admin_print_footer_scripts', function () use ($script) {
                        echo $script;
                    });
                }

                continue;
            }

            $num = 0;

            foreach ($files as $key => $opts) {
                $opts = array_merge($defaults, $opts);

                if ('js' === $type && !$num) {
                    array_unshift($opts['deps'], $defaults['dependency']);
                    $num++;
                }

                $func = 'js' === $type ? 'wp_enqueue_script' : 'wp_enqueue_style';
                $src  = self::copyFile($opts['src'], $type);
                $last = 'js' === $type ? $opts['in_footer'] : $opts['media'];

                $func($key, $src, $opts['deps'], $opts['ver'], $last);
            }
        }
    }

    /**
     * Enqueue scripts and styles.
     *
     * @param  string  $path
     * @param  string  $folder
     *
     * @return string
     */
    public static function copyFile($path, $folder) : string
    {
        // Update details
        $basename = basename($path);
        $source   = rtrim(dirname($path), S);
        $target   = rtrim(OL_ZEUS_DISTPATH, S).S.$folder;

        // Update file path on dist accessible folder
        Helpers::copyFile($source, $target, $basename);

        // Return file uri
        return esc_url(OL_ZEUS_URI.$folder.S.$basename);
    }

    /**
     * Retrieve Section translations
     *
     * @throws SectionException
     *
     * @return array
     */
    public static function translate() : array
    {
        // Get instance
        try {
            $section = self::getInstance();
        } catch (Exception $e) {
            throw new SectionException(Translate::t('section.errors.class_is_not_defined'));
        }

        // Set translations
        $class = $section->getClass();

        return [
            $section->textdomain => dirname(dirname($class['resources'])).S.'languages'
        ];
    }
}
