<?php

/**
 * Template Name: Zeus Login
 *
 * Template to display the WordPress Login page in the WordPress Customizer.
 *
 * @package    OlympusZeusCore
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

$action = 'login';

// Include helpers
require_once __DIR__.S.'_helpers.php';

/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form. Actions include 'postpass',
 * 'logout', 'lostpassword', etc.
 *
 * @since 2.8.0
 */
do_action('login_form_login');

/**
 * Filters the separator used between login form navigation links.
 */
$login_link_separator = apply_filters('login_link_separator', ' | ');

// Manage errors
$aria_describedby_error = $errors->has_errors() ? ' aria-describedby="login_error"' : '';

// Form action
$formaction = esc_url(site_url('wp-login.php', 'login_post'));

// Header
zeus_login_header(__('Log In'), '', $errors);

?>

<form name="loginform" id="loginform" action="<?php echo $formaction ?>" method="post" autocomplete="off">
    <p>
        <label for="user_login">
            <?php _e('Username or Email Address') ?><br />
            <input type="text" name="log" id="user_login"<?php echo $aria_describedby_error ?> class="input" value="" size="20" autocapitalize="off" />
        </label>
    </p>
    <p>
        <label for="user_pass">
            <?php _e('Password') ?><br />
            <input type="password" name="pwd" id="user_pass"<?php echo $aria_describedby_error ?> class="input" value="" size="20" />
        </label>
    </p>

    <?php
    /**
     * Fires following the 'Password' field in the login form.
     *
     * @since 2.1.0
     */
    do_action('login_form');
    ?>

    <p class="forgetmenot">
        <label for="rememberme">
            <input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e('Remember Me') ?>
        </label>
    </p>

    <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Log In') ?>" />
        <input type="hidden" name="customize-login" value="1" />
        <input type="hidden" name="testcookie" value="1" />
    </p>
</form>

<p id="nav">
    <?php
    if (get_option('users_can_register')) {
        $registration_url = sprintf('<a href="%s">%s</a>', esc_url(wp_registration_url()), __('Register'));

        /** This filter is documented in wp-includes/general-template.php */
        echo apply_filters('register', $registration_url);

        echo esc_html($login_link_separator);
    }
    ?>

    <a href="<?php echo esc_url(wp_lostpassword_url()) ?>"><?php _e('Lost your password?') ?></a>
</p>

<?php

// Footer
zeus_login_footer('user_login');
