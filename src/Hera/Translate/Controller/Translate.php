<?php

namespace GetOlympus\Hera\Translate\Controller;

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

class Translate
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
            OLH_PATH.S.'Field'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Menu'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Metabox'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Posttype'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Template'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Term'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
            OLH_PATH.S.'Widget'.S.'Resources'.S.'languages'.S.$lang.'.yaml',
        ];

        // Define Translator
        $this->translator = new Translator($lang);
        $this->translator->addLoader('yaml', new YamlFileLoader());

        // Add Hera core languages in `core` dictionary
        foreach ($yamls as $path) {
            $this->translator->addResource('yaml', $path, $lang, 'core');
        }

        /**
         * Add your custom languages with alias.
         *
         * @param Translator $translator
         */
        do_action('olh_translate_resource', $this->translator);
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
     * Translate typo.
     *
     * @param string $content
     * @param array $args
     * @param string $alias
     * @return Translate
     */
    public static function t($content, $args = [], $alias = 'core')
    {
        return self::getInstance()->translator->trans($content, $args, $alias);
    }
}
