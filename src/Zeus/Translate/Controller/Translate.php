<?php

namespace GetOlympus\Zeus\Translate\Controller;

use GetOlympus\Zeus\Translate\Controller\TranslateInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Translates typos.
 *
 * @package    OlympusZeusCore
 * @subpackage Translate\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
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
        $local = str_replace('-', '_', OL_ZEUS_LOCAL);

        // Get all available locals
        $availables = [
            'en_EN'
            // Other languages will be available very soon!
        ];

        // Check local
        $lang = !in_array($local, $availables) ? $availables[0] : $local;

        // Build all YAML files to add
        $yamls = [
            OL_ZEUS_PATH.S.'AdminPage'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Configuration'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Field'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Metabox'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Posttype'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Term'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'User'.S.'Resources'.S.'languages' => 'core',
            OL_ZEUS_PATH.S.'Widget'.S.'Resources'.S.'languages' => 'core',
        ];

        /**
         * Add your custom languages with alias.
         *
         * @param   array $yamls
         * @return  array $yamls
         */
        $yamls = apply_filters('ol_zeus_translate_resources', $yamls);

        // Define Translator
        $this->translator = new Translator($lang, new MessageSelector(), OL_ZEUS_CACHE);
        $this->translator->addLoader('yaml', new YamlFileLoader());

        // Add languages in `core` dictionary
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
     * Choice typo.
     *
     * @param   string  $message
     * @param   integer $number
     * @param   array   $args
     * @param   string  $domain
     * @param   string  $locale
     * @return  string
     */
    public static function c($message, $number, $args = [], $domain = 'core', $locale = 'en_EN')
    {
        return self::getInstance()->translator->transChoice($message, $number, $args, $domain, $locale);
    }

    /**
     * Noop typo from WordPress.
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
     * @param   string  $message
     * @param   array   $args
     * @param   string  $domain
     * @param   string  $locale
     * @return  Translate
     */
    public static function t($message, $args = [], $domain = 'core', $locale = 'en_EN')
    {
        return self::getInstance()->translator->trans($message, $args, $domain, $locale);
    }
}
