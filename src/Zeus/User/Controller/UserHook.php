<?php

namespace GetOlympus\Zeus\User\Controller;

use GetOlympus\Zeus\Field\Controller\Field;
use GetOlympus\Zeus\Option\Controller\Option;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Request\Controller\Request;
use GetOlympus\Zeus\Translate\Controller\Translate;
use GetOlympus\Zeus\User\Controller\UserHookInterface;

/**
 * Works with User Engine.
 *
 * @package    OlympusZeusCore
 * @subpackage User\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.6
 *
 */

class UserHook implements UserHookInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var string
     */
    protected $title;

    /**
     * Constructor.
     *
     * @param string    $title
     * @param array     $fields
     */
    public function __construct($title = '', $fields = [])
    {
        // Check fields
        if (empty($fields)) {
            return;
        }

        $this->fields = $fields;
        $this->title = $title;

        // Save or show custom fields
        if (OL_ZEUS_ISADMIN) {
            // Render assets
            Render::assets(['profile.php'], $this->fields);

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
     * @param integer $user_id
     */
    public function saveProfileFields($user_id)
    {
        // Get contents
        $fields = $this->fields;

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

            // Build contents
            $ctn = (array) $field->getModel()->getContents();
            $hasId = (boolean) $field->getModel()->getHasId();

            // Check ID
            if ($hasId && (!isset($ctn['id']) || empty($ctn['id']))) {
                continue;
            }

            $value = Request::post($ctn['id']);
            Option::updateAuthorMeta($user_id, $ctn['id'], $value);
        }
    }

    /**
     * Hook to display user custom fields.
     *
     * @param object $user
     */
    public function showProfileFields($user)
    {
        // Get contents
        $fields = $this->fields;
        $title = $this->title;

        // Check fields
        if (empty($fields)) {
            return;
        }

        $vars = [
            't_user_title' => !empty($title) ? $title : Translate::t('user.title'),
        ];

        // Get all values
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            $vars['fields'][] = $field->render([], [
                'template' => 'user',
                'user' => $user,
            ], false);
        }

        // Render view
        Render::view('user.html.twig', $vars, 'user');
    }
}
