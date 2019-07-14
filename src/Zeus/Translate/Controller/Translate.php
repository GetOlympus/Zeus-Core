<?php

namespace GetOlympus\Zeus\Translate\Controller;

use GetOlympus\Zeus\Translate\Implementation\TranslateImplementation;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * Translates typos.
 *
 * @package    OlympusZeusCore
 * @subpackage Translate\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Translate implements TranslateImplementation
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
        // Define Translator
        $this->translator = new Translator($lang, new MessageSelector(), OL_ZEUS_CACHE);
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
     * Load translations.
     *
     * @param  array   $translations
     * @param  string  $locale
     */
    public static function l($translations = [], $locale = 'default')
    {
        if (empty($translations)) {
            return;
        }

        // Define current locale
        $currentlocale = determine_locale();

        // Create MO file if current locale does not exist
        add_action('load_textdomain_mofile', function ($mofile, $domain) use ($translations, $currentlocale, $locale) {
            // Check if domain is concerned by translations
            if (!array_key_exists($domain, $translations)) {
                return $mofile;
            }

            // Change locale if needed
            if (!file_exists($mofile)) {
                $mofile = str_replace($currentlocale.'.mo', $locale.'.mo', $mofile);
            }

            return $mofile;
        }, 10, 2);

        // Iterate
        foreach ($translations as $domain => $languages) {
            load_textdomain($domain, rtrim($languages, S).S.$domain.'-'.$currentlocale.'.mo');
        }
    }

    /**
     * Noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @param  integer $number
     * @param  string  $domain
     * @return string
     */
    public static function n($single, $plural, $number = 1, $domain = 'olympus-zeus')
    {
        return _n($single, $plural, $number, $domain);
    }

    /**
     * Prepare noop typo from WordPress.
     *
     * @param  string  $single
     * @param  string  $plural
     * @return string
     */
    public static function noop($single, $plural)
    {
        return _n_noop($single, $plural);
    }

    /**
     * Translate typo.
     *
     * @param  string  $message
     * @param  string  $domain
     * @return string
     */
    public static function t($message, $domain = 'olympus-zeus')
    {
        return __($message, $domain);
    }
}
