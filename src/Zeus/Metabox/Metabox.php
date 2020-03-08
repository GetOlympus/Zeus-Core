<?php

namespace GetOlympus\Zeus\Metabox;

use GetOlympus\Zeus\Base\Base;
use GetOlympus\Zeus\Metabox\MetaboxException;
use GetOlympus\Zeus\Metabox\MetaboxInterface;
use GetOlympus\Zeus\Metabox\MetaboxModel;
use GetOlympus\Zeus\Utils\Helpers;
use GetOlympus\Zeus\Utils\Render;
use GetOlympus\Zeus\Utils\Translate;

/**
 * Gets its own post type.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Metabox extends Base implements MetaboxInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new MetaboxModel();
    }

    /**
     * Build Metabox component.
     *
     * @param  string  $title
     * @param  array   $fields
     *
     * @throws MetaboxException
     *
     * @return Metabox
     */
    public static function build($title, $fields = []) : Metabox
    {
        // Get instance
        $metabox = self::getInstance();

        // Check fields
        if (empty($fields)) {
            throw new MetaboxException(Translate::t('metabox.errors.no_fields'));
        }

        // Define ID
        $id = Helpers::urlize($title);

        // Set details
        $metabox->getModel()->setId($id);
        $metabox->getModel()->setTitle($title);
        $metabox->getModel()->setFields($fields);

        // Get metabox
        return $metabox;
    }

    /**
     * Initialization.
     *
     * @param  string  $identifier
     * @param  string  $slug
     */
    public function init($identifier, $slug) : void
    {
        $this->getModel()->setId($identifier);
        $this->getModel()->setSlug($slug);

        $this->addMetabox();
    }

    /**
     * Add metabox.
     */
    public function addMetabox() : void
    {
        $fields = $this->getModel()->getFields();

        // Add meta box
        add_meta_box(
            $this->getModel()->getId(),
            $this->getModel()->getTitle(),
            [$this, 'callback'],
            $this->getModel()->getSlug(),
            $this->getModel()->getContext(),
            $this->getModel()->getPriority(),
            $fields
        );
    }

    /**
     * Callback function.
     *
     * @param  array   $post
     * @param  array   $args
     *
     * @throws MetaboxException
     *
     * @return int|null
     */
    public function callback($post, $args) : ?int
    {
        // If autosave...
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return isset($post->ID) ? $post->ID : null;
        }

        // Get field
        $fields = isset($args['args']) ? $args['args'] : null;

        // Check if fields are defined
        if (!$fields || empty($fields)) {
            throw new MetaboxException(Translate::t('metabox.errors.no_type_is_defined'));
        }

        // Prepare admin scripts and styles
        $assets = [
            'scripts' => [],
            'styles'  => [],
        ];

        $vars = [];

        // Display fields
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

            $vars['fields'][] = $field->prepare('metabox', $post, 'post');
        }

        // Render view
        $render = new Render('core', 'layouts'.S.'metabox.html.twig', $vars, $assets);
        $render->view();

        // Return post if it is asked
        return isset($post->ID) ? $post->ID : null;
    }
}
