<?php

/**
 * Usefull helper for Zeus Login and Registration pages
 *
 * @package    OlympusZeusCore
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

// Redirect user if this page is viewed from outside the WP Customizer
if (!is_customize_preview()) {
    // Get panel from CustomizerHook
    $panel = isset($_REQUEST['panel-redirect']) ? $_REQUEST['panel-redirect'] : '';

    // Generate the redirect url
    wp_safe_redirect(add_query_arg(
        [
            'autofocus[panel]' => $panel,
            'return'           => admin_url('index.php'),
            'url'              => rawurlencode($page),
        ],
        admin_url('customize.php')
    ));
}

// Redirect to https login if forced to use SSL
if (force_ssl_admin() && !is_ssl()) {
    $host = $_SERVER['HTTP_HOST'];
    $uri  = $_SERVER['REQUEST_URI'];

    // Build redirect URL
    $is_https = 0 === strpos($uri, 'http');
    $redirect = $is_https ? set_url_scheme($uri, 'https') : 'https://'.$host.$uri;

    // Make redirection
    wp_redirect($redirect);
    exit();
}

// Errors
$errors = new WP_Error();

// Make sure that the WordPress bootstrap has run before continuing.
//require_once ABSPATH.S.'wp-load.php';

/**
 * Output login page header.
 * @see https://developer.wordpress.org/reference/functions/login_header/
 *
 * @since 2.1.0
 *
 * @param  string   $title
 * @param  string   $message
 * @param  WP_Error $wp_error
 */
function zeus_login_header($title = 'Log In', $message = '', $wp_error = null)
{
    global $error, $interim_login, $action;

    // Set WP_Error object
    $wp_error = empty($wp_error) ? new WP_Error() : $wp_error;

    // Change viewport
    add_action('login_head', function () {
        echo '<meta name="viewport" content="width=device-width" />';
    });

    // Do not index contents
    add_action('login_head', 'wp_no_robots');

    /**
     * 1st step:
     * Usefull definitions to avoid multiple database queries.
     */

    // Check multisite context
    $is_multisite = is_multisite();

    // Check language attributes
    $language_attributes = get_language_attributes();

    // Check usefull blog definitions
    $blogname     = get_bloginfo('name', 'display');
    $bloghtml     = get_bloginfo('html_type');
    $blogcharset  = get_bloginfo('charset');

    // Set usefull functions for heredoc
    $esc_attr = 'esc_attr';
    $esc_url  = 'esc_url';

    /**
     * 2nd step:
     * Works on Login title and display header HTML tags with enqueued Login styles and scripts.
     */

    $login_title = $blogname;

    /* translators: Login screen title. 1: Login screen name, 2: Network or site name */
    $login_title = sprintf(__('%1$s &lsaquo; %2$s &#8212; WordPress'), $title, $login_title);

    /**
     * Filters the title tag content for login page.
     *
     * @since 4.9.0
     *
     * @param string $login_title The page title, with extra context added.
     * @param string $title       The original page title.
     */
    $login_title = apply_filters('login_title', $login_title, $title);

    $content = <<<EOT
    <!DOCTYPE html>
    <!--[if IE 8]><html xmlns="http://www.w3.org/1999/xhtml" class="ie8" $language_attributes><![endif]-->
    <!--[if !(IE 8) ]><!--><html xmlns="http://www.w3.org/1999/xhtml" $language_attributes><!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="$bloghtml; charset=$blogcharset" />
        <title>$login_title</title>
    EOT;

    echo $content;

    wp_enqueue_style('login');

    /**
     * Enqueue scripts and styles for the login page.
     *
     * @since 3.1.0
     */
    do_action('login_enqueue_scripts');

    /**
     * Fires in the login page header after scripts are enqueued.
     *
     * @since 2.1.0
     */
    do_action('login_head');

    /**
     * 3rd step:
     * Works on login header url, title and text.
     */

    $login_header_url   = $is_multisite ? network_home_url() : __('https://wordpress.org/');
    $login_header_title = $is_multisite ? get_network()->site_name : __('Powered by WordPress');

    /**
     * Filters link URL of the header logo above login form.
     *
     * @since 2.1.0
     *
     * @param string $login_header_url Login header logo URL.
     */
    //$login_header_url   = apply_filters('login_headerurl', $login_header_url);

    /**
     * Filters the title attribute of the header logo above login form.
     *
     * @since 2.1.0
     *
     * @param string $login_header_title Login header logo title attribute.
     */
    $login_header_title = apply_filters('login_headertitle', $login_header_title);

    /*
     * To match the URL/title set above, Multisite sites have the blog name,
     * while single sites get the header title.
     */
    $login_header_text  = $is_multisite ? $blogname : $login_header_title;

    /**
     * 4th step:
     * Works on CSS body tag classes and display body HTML tag.
     */

    $classes = [
        'login-action-'.$action,
        'wp-core-ui',
        is_rtl() ? 'rtl' : 'ltr',
    ];

    if ($interim_login) {
        $classes[] = 'interim-login';

        if ('success' ===  $interim_login) {
            $classes[] = 'interim-login-success';
        }
    }

    $classes[] = 'locale-'.sanitize_html_class(strtolower(str_replace('_', '-', get_locale())));

    /**
     * Filters the login page body classes.
     *
     * @since 3.5.0
     *
     * @param array  $classes An array of body classes.
     * @param string $action  The action that brought the visitor to the login page.
     */
    $classes = apply_filters('login_body_class', $classes, $action);

    $content = <<<EOT
        <style>html{background-color:transparent}</style>
    </head>
    <body class="login {$esc_attr(implode(' ', $classes))}">
    EOT;

    echo $content."\n";

    /**
     * 5th step:
     * Works on Login header.
     */

    /**
     * Fires in the login page header after the body tag is opened.
     *
     * @since 4.6.0
     */
    do_action('login_header');

    $content = <<<EOT
    <div id="login">
        <h1>
            <a href="{$esc_url($login_header_url)}" title="{$esc_attr($login_header_title)}" tabindex="-1">
                $login_header_text
            </a>
        </h1>
    EOT;

    echo $content."\n";

    /**
     * 6th step:
     * Works on message errors.
     */

    /**
     * Filters the message to display above the login form.
     *
     * @since 2.1.0
     *
     * @param string $message Login message text.
     */
    $message = apply_filters('login_message', $message);
    echo !empty($message) ? $message."\n" : '';

    // In case a plugin uses $error rather than the $wp_errors object.
    if (!empty($error)) {
        $wp_error->add('error', $error);
        unset($error);
    }

    if ($wp_error->has_errors()) {
        $errors   = '';
        $messages = '';

        foreach ($wp_error->get_error_codes() as $code) {
            $severity = $wp_error->get_error_data($code);

            foreach ($wp_error->get_error_messages($code) as $error_message) {
                if ('message' === $severity) {
                    $messages .= '  '.$error_message.'<br/>'."\n";
                    continue;
                }

                $errors .= '    '.$error_message.'<br/>'."\n";
            }
        }

        /**
         * Filters the error messages displayed above the login form.
         *
         * @since 2.1.0
         *
         * @param string $errors Login error message.
         */
        echo !empty($errors) ? '<div id="login_error">'.apply_filters('login_errors', $errors).'</div>'."\n" : '';

        /**
         * Filters instructional messages displayed above the login form.
         *
         * @since 2.5.0
         *
         * @param string $messages Login messages.
         */
        echo !empty($messages) ? '<p class="message">'.apply_filters('login_messages', $messages).'</p>'."\n" : '';
    }
}

/**
 * Outputs the footer for the login page.
 * @see https://developer.wordpress.org/reference/functions/login_footer/
 *
 * @since 3.1.0
 *
 * @param string $input_id Which input to auto-focus
 */
function zeus_login_footer($input_id = '')
{
    global $interim_login;

    // Set usefull functions for heredoc
    $esc_attr = 'esc_attr';
    $esc_url  = 'esc_url';

    // Don't allow interim logins to navigate away from the page.
    if (!$interim_login) {
        $backto = sprintf(_x('&larr; Back to %s', 'site'), get_bloginfo('title', 'display'));

        $content = <<<EOT
        <p id="backtoblog">
            <a href="{$esc_url(home_url('/'))}">$backto</a>
        </p>
        EOT;

        echo $content."\n";

        the_privacy_policy_link('<div class="privacy-policy-page-link">', '</div>'."\n");
    }

    echo '</div>'."\n";

    if (!empty($input_id)) {
        $content = <<<EOT
        <script type="text/javascript">
            try{document.getElementById('$input_id').focus();}catch(e){}
            if(typeof wpOnload=='function')wpOnload();
        </script>
        EOT;

        echo $content."\n";
    }

    /**
     * Fires in the login page footer.
     *
     * @since 3.1.0
     */
    do_action('login_footer');

    echo '<div class="clear"></div>'."\n";
    echo '</body>'."\n";
    echo '</html>'."\n";
}

// Headers
nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

/**
 * Fires when the login form is initialized.
 *
 * @since 3.2.0
 */
do_action('login_init');

/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form. Actions include 'postpass',
 * 'logout', 'lostpassword', etc.
 *
 * @since 2.8.0
 */
do_action("login_form_{$action}");
