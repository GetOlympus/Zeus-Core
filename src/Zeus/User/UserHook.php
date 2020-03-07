<?php

namespace GetOlympus\Zeus\User;

use GetOlympus\Zeus\Utils\Option;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Request;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Works with User Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage User
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

class UserHook
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param  User    $user
     */
    public function __construct($user)
    {
        $fields = $user->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
        }

        $this->user = $user;

        // Save or show custom fields
        if (OL_ZEUS_ISADMIN) {
            // Save
            add_action('personal_options_update', [$this, 'saveProfileFields']);
            add_action('edit_user_profile_update', [$this, 'saveProfileFields']);

            // Show
            add_action('show_user_profile', [$this, 'showProfileFields']);
            add_action('edit_user_profile', [$this, 'showProfileFields']);
        }
    }

    /**
     * Hook to save user custom fields.
     *
     * @param  int     $user_id
     *
     * @return bool
     */
    public function saveProfileFields($user_id) : bool
    {
        // Get contents
        $fields = $this->user->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return false;
        }

        // Check if current user can edit profile
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // Get all values
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            $id = (string) $field->getModel()->getIdentifier();

            if (empty($id)) {
                continue;
            }

            $value = Request::post($id);
            Option::updateAuthorMeta($user_id, $id, $value);
        }

        return true;
    }

    /**
     * Hook to display user custom fields.
     *
     * @param  object  $user
     */
    public function showProfileFields($user) : void
    {
        // Get contents
        $fields = $this->user->getModel()->getFields();
        $title = $this->user->getModel()->getTitle();

        // Check fields
        if (empty($fields)) {
            return;
        }

        $vars = [
            't_user_title' => !empty($title) ? $title : Translate::t('user.labels.title'),
        ];

        // Prepare admin scripts and styles
        $assets = [
            'scripts' => [],
            'styles'  => [],
        ];

        // Get all values
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            // Update scripts and styles
            $fieldassets = $field->assets();

            if (!empty($fieldassets)) {
                $assets['scripts'] = array_merge($assets['scripts'], $fieldassets['scripts']);
                $assets['styles']  = array_merge($assets['styles'], $fieldassets['styles']);
            }

            // Prepare fields to be displayed
            $vars['fields'][] = $field->prepare('user', $user, 'user');
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'user.html.twig', $vars, $assets);
        $render->view();
    }
}
