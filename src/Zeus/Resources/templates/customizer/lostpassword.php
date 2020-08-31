<?php

/**
 * Template Name: Zeus Lost Password
 *
 * Template to display the WordPress Lost Password page in the WordPress Customizer.
 *
 * @package    OlympusZeusCore
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.1
 *
 */

$action = 'lostpassword';

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
do_action('login_form_lostpassword');

/**
 * Fires before the lost password form.
 *
 * @since 1.5.1
 * @since 5.1.0 Added the `$errors` parameter.
 *
 * @param WP_Error $errors A `WP_Error` object containing any errors generated by using invalid
 *                         credentials. Note that the error object may not contain any errors.
 */
do_action('lost_password', $errors);

/**
 * Filters the separator used between login form navigation links.
 */
$login_link_separator = apply_filters('login_link_separator', ' | ');

// Form action
$formaction = esc_url(network_site_url('wp-login.php?action=lostpassword', 'login_post'));

// Header
zeus_login_header(__('Lost Password'), '<p class="message">'.__('Please enter your username or email address. You will receive a link to create a new password via email.').'</p>', $errors);

?>

<?php  ?>

<form name="lostpasswordform" id="lostpasswordform" action="<?php echo $formaction ?>" method="post">
    <p>
        <label for="user_login">
            <?php _e('Username or Email Address') ?><br />
            <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" />
        </label>
    </p>

    <?php
    /**
     * Fires inside the lostpassword form tags, before the hidden fields.
     *
     * @since 2.1.0
     */
    do_action('lostpassword_form');
    ?>

    <input type="hidden" name="redirect_to" value="" />

    <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Get New Password') ?>" />
    </p>
</form>

<p id="nav">
    <a href="<?php echo esc_url(wp_login_url()) ?>"><?php _e('Log in') ?></a>

    <?php
    if (get_option('users_can_register')) {
        $registration_url = sprintf('<a href="%s">%s</a>', esc_url(wp_registration_url()), __('Register'));

        echo esc_html($login_link_separator);

        /**
         * This filter is documented in wp-includes/general-template.php
         */
        echo apply_filters('register', $registration_url);
    }
    ?>
</p>

<?php

// Footer
zeus_login_footer('user_login');
