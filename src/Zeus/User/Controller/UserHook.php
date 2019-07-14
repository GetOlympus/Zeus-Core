<?php

namespace GetOlympus\Zeus\User\Controller;

use GetOlympus\Zeus\Field\Controller\Field;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;
use GetOlympus\Zeus\User\Implementation\UserHookImplementation;

/**
 * Works with User Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

class UserHook implements UserHookImplementation
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
            add_action('personal_options_update', [&$this, 'saveProfileFields']);
            add_action('edit_user_profile_update', [&$this, 'saveProfileFields']);

            // Show
            add_action('show_user_profile', [&$this, 'showProfileFields']);
            add_action('edit_user_profile', [&$this, 'showProfileFields']);
        }
    }

    /**
     * Hook to save user custom fields.
     *
     * @param  integer $user_id
     */
    public function saveProfileFields($user_id)
    {
        // Get contents
        $fields = $this->user->getModel()->getFields();

        // Check fields
        if (empty($fields)) {
            return;
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
    }

    /**
     * Hook to display user custom fields.
     *
     * @param  object  $user
     */
    public function showProfileFields($user)
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
            $vars['fields'][] = $field->prepare('user');
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'user.html.twig', $vars, $assets);
        $render->view();
    }
}
