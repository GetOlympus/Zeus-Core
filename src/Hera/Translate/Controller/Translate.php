<?php

namespace GetOlympus\Hera\Translate\Controller;

use GetOlympus\Hera\Translate\Controller\TranslateInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Translates typos.
 *
 * @package Olympus Hera
 * @subpackage Translate\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Translate implements TranslateInterface
{
    /**
     * @var Singleton
     */
    private static $instance;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Get global local used
        $local = str_replace('-', '_', OLH_LOCAL);

        // Get all available Hera locals
        $availables = [
            'en_EN'
            // Other languages will be available soon!
        ];

        // Check local
        $lang = !in_array($local, $availables) ? $availables[0] : $local;

        // Build all YAML files to add
        $yamls = [
            OLH_PATH.S.'Configuration'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Field'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Menu'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Metabox'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Posttype'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Template'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Term'.S.'Resources'.S.'languages' => 'core',
            OLH_PATH.S.'Widget'.S.'Resources'.S.'languages' => 'core',
        ];

        /**
         * Add your custom languages with alias.
         *
         * @param   array $yamls
         * @return  array $yamls
         */
        $yamls = apply_filters('olh_translate_resources', $yamls);

        // Define Translator
        $this->translator = new Translator($lang);
        $this->translator->addLoader('yaml', new YamlFileLoader());

        // Add Hera core languages in `core` dictionary
        foreach ($yamls as $path => $package) {
            $file = $path.S.$lang.'.yaml';
            $this->translator->addResource('yaml', $file, $lang, $package);
        }
    }

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
     * Noop typo.
     *
     * @param   string $singular
     * @param   string $plural
     * @return  string
     */
    public static function n($singular, $plural)
    {
        return _n_noop($singular, $plural);
    }

    /**
     * Translate typo.
     *
     * @param   string  $content
     * @param   array   $args
     * @param   string  $alias
     * @return  Translate
     */
    public static function t($content, $args = [], $alias = 'core')
    {
        return self::getInstance()->translator->trans($content, $args, $alias);
    }
}
