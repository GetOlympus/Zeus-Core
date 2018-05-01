<?php

namespace GetOlympus\Zeus\Configuration\Controller;

use GetOlympus\Zeus\Configuration\Controller\Configuration;
use GetOlympus\Zeus\Helpers\Controller\Helpers;

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
     * @var array
     */
    protected $available = [
        'access-urls',
        'login-error',
        'login-header',
        'login-shake',
        'login-style',
    ];

    /**
     * Add all usefull WP filters and hooks.
     */
    public function init()
    {
        // Check filepath
        if (empty($this->filepath)) {
            return;
        }

        // Get configurations
        $configs = include $this->filepath;

        // Check
        if (empty($configs)) {
            return;
        }

        // Iterate on configs
        foreach ($configs as $key => $args) {
            if (!in_array($key, $this->available) || empty($args)) {
                continue;
            }

            $func = Helpers::toFunctionFormat($key).'Setting';
            $this->$func($args);
        }
    }

    /**
     * Hiding wp-login.php/wp-register.php in the login and registration URLs
     *
     * @param array $args
     */
    public function accessUrlsSetting($args)
    {
        if (!$args) {
            return;
        }

        // Define defaults
        $configs = array_merge([
            'login'         => '',
            'logout'        => '',
            'lostpassword'  => '',
            'register'      => '',
        ], $args);

        // Change login URL
        add_filter('login_redirect', function ($url) use ($configs) {
            return empty($configs['login']) ? $url : site_url().$configs['login'];
        });

        // Customize Site URL
        add_filter('site_url', function ($url, $path, $scheme = null) use ($configs) {
            $pattern = [
                'login'         => '/wp-login.php',
                'logout'        => '/wp-login.php?action=logout',
                'lostpassword'  => '/wp-login.php?action=lostpassword',
                'register'      => '/wp-login.php?action=register',
            ];

            // Iterate on all queries and replace the current Site URL
            foreach ($pattern as $key => $query) {
                if (empty($configs[$key])) {
                    continue;
                }

                $url = str_replace($query, $configs[$key], $url);
            }

            return $url;
        }, 10, 3);

        // Make the redirection works properly
        add_filter('wp_redirect', function ($url, $status) use ($configs) {
            // Check login configuration
            if (empty($configs['login'])) {
                return $url;
            }

            $triggers = [
                'wp-login.php?checkemail=confirm',
                'wp-login.php?checkemail=registered',
            ];

            foreach ($triggers as $trigger) {
                if ($url !== $trigger) {
                    continue;
                }

                return str_replace('wp-login.php', site_url().$configs['login'], $url);
            }

            return $url;
        }, 10, 2);
    }

    /**
     * Redisign wp-login.php page with custom error message.
     *
     * @param string|boolean $error
     */
    public function loginErrorSetting($error)
    {
        // Define default message
        $message = is_bool($error) && $error ? Translate::t('configuration.settings.login.error') : $error;

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
    public function loginHeaderSetting($args)
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
        add_filter('login_headerurl', function ($url) use ($configs) {
            if (!empty($configs['url'])) {
                return $configs['url'];
            }
        });

        // Change login head title
        add_filter('login_headertitle', function ($title) use ($configs) {
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
    public function loginShakeSetting($shake)
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
    public function loginStyleSetting($args)
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
            if (!empty($configs['scripts'])) {
                return;
            }

            foreach ($configs['scripts'] as $script) {
                if (3 !== count($script)) {
                    continue;
                }

                wp_enqueue_script($script[0], $script[1], $script[2]);
            }
        }, 10);

        add_action('login_enqueue_scripts', function () use ($configs) {
            if (!empty($configs['styles'])) {
                return;
            }

            foreach ($configs['styles'] as $style) {
                if (3 !== count($style)) {
                    continue;
                }

                wp_enqueue_style($style[0], $style[1], $style[2]);
            }
        }, 9);
    }
}
