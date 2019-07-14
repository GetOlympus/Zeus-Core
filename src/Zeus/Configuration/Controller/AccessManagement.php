<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;

/**
 * Access Management controller
 *
 * @package    OlympusZeusCore
 * @subpackage Configuration\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.46
 *
 */

class AccessManagement extends Configuration
{
    /**
     * Add all usefull WP filters and hooks.
     */
    public function init()
    {
        // Initialize filepath with configs
        $funcs = $this->getFunctions('AccessManagement', [
            'access-url',
            'login-error',
            'login-header',
            'login-shake',
            'login-style',
        ]);

        // Check functions
        if (empty($funcs)) {
            return;
        }

        // Iterate on functions
        foreach ($funcs as $key => $args) {
            $this->$key($args);
        }
    }

    /**
     * Hiding `wp-login.php` in the login and registration URLs
     *
     * @param string $slug
     */
    public function accessUrlAccessManagement($slug)
    {
        $wplogin = 'wp-login.php';

        if (empty($slug) || $wplogin === $slug) {
            return;
        }

        // Customize Network site and Site URLs
        add_filter('network_site_url', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });
        add_filter('site_url', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });

        // Manage redirects
        add_filter('login_redirect', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });
        add_filter('logout_redirect', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });
        add_filter('lostpassword_redirect', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });

        // Make the redirection works properly
        add_filter('wp_redirect', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });
        add_filter('login_url', function ($url) use ($slug, $wplogin) {
            return str_replace($wplogin, $slug, $url);
        });

        // Update form on wp-login page
        add_action('login_form', function () use ($slug, $wplogin) {
            $form_content = ob_get_contents();
            $form_content = str_replace($wplogin, $slug, $form_content);
            ob_get_clean();
            echo $form_content;
        }, 1);
    }

    /**
     * Redisign wp-login.php page with custom error message.
     *
     * @param string $message
     */
    public function loginErrorAccessManagement($message)
    {
        // Define default message
        $message = $message ? $message : Translate::t('configuration.access.errors.incorrect_login');

        // Change login error message
        add_filter('login_errors', function () use ($message) {
            if (!empty($message)) {
                return $message;
            }
        });
    }

    /**
     * Redisign wp-login.php page with custom header configurations.
     *
     * @param array $args
     */
    public function loginHeaderAccessManagement($args)
    {
        if (!$args) {
            return;
        }

        // Define defaults
        $configs = array_merge([
            'url'     => OL_ZEUS_HOME,
            'title'   => OL_ZEUS_NAME,
        ], $args);

        // Change login head URL
        add_filter('login_headerurl', function () use ($configs) {
            if (!empty($configs['url'])) {
                return $configs['url'];
            }
        });

        // Change login head title
        add_filter('login_headertext', function () use ($configs) {
            if (!empty($configs['title'])) {
                return $configs['title'];
            }
        });
    }

    /**
     * Define wether if WP has to shake the login box or not.
     *
     * @param boolean $shake
     */
    public function loginShakeAccessManagement($shake)
    {
        if ($shake) {
            return;
        }

        add_action('login_head', function () {
            remove_action('login_head', 'wp_shake_js', 12);
        });
    }

    /**
     * Redisign wp-login.php page with custom style and scripts.
     *
     * @param array $args
     */
    public function loginStyleAccessManagement($args)
    {
        if (!$args) {
            return;
        }

        // Define defaults
        $configs = array_merge([
            'scripts'   => [],
            'styles'    => [],
        ], $args);

        // Render assets

        add_action('login_enqueue_scripts', function () use ($configs) {
            if (empty($configs['scripts'])) {
                return;
            }

            foreach ($configs['scripts'] as $script) {
                if (3 > count($script)) {
                    continue;
                }

                wp_enqueue_script($script[0], $script[1], $script[2]);
            }
        }, 10);

        add_action('login_enqueue_scripts', function () use ($configs) {
            if (empty($configs['styles'])) {
                return;
            }

            foreach ($configs['styles'] as $style) {
                if (3 > count($style)) {
                    continue;
                }

                wp_enqueue_style($style[0], $style[1], $style[2]);
            }
        }, 9);
    }
}
