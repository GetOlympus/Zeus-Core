<?php

/**
 * Template Name: Zeus Registration
 *
 * Template to display the WordPress Registration page in the WordPress Customizer.
 *
 * @package    OlympusZeusCore
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

$action = 'register';

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
do_action('login_form_register');

/**
 * Filters the separator used between login form navigation links.
 */
$login_link_separator = apply_filters('login_link_separator', ' | ');

// Manage errors
$aria_describedby_error = $errors->has_errors() ? ' aria-describedby="login_error"' : '';

// Form action
$formaction = esc_url(site_url('wp-login.php?action=register', 'login_post'));

// Header
zeus_login_header(__('Registration Form'), '<p class="message register">'.__('Register For This Site').'</p>', $errors);

?>

<form name="registerform" id="registerform" action="<?php echo $formaction ?>" method="post" novalidate="novalidate">
    <p>
        <label for="user_login">
            <?php _e('Username') ?><br />
            <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" />
        </label>
    </p>
    <p>
        <label for="user_email">
            <?php _e('Email') ?><br />
            <input type="email" name="user_email" id="user_email" class="input" value="" size="25" />
        </label>
    </p>

    <?php
    /**
     * Fires following the 'Email' field in the user registration form.
     *
     * @since 2.1.0
     */
    do_action('register_form');
    ?>

    <p id="reg_passmail">
        <?php _e('Registration confirmation will be emailed to you.') ?>
    </p>

    <br class="clear" />

    <input type="hidden" name="redirect_to" value="" />

    <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Register') ?>" />
    </p>
</form>

<p id="nav">
    <a href="<?php echo esc_url(wp_login_url()) ?>"><?php _e('Log in') ?></a>
    <?php echo esc_html($login_link_separator) ?>
    <a href="<?php echo esc_url(wp_lostpassword_url()) ?>"><?php _e('Lost your password?') ?></a>
</p>

<?php

// Footer
zeus_login_footer('user_login');
