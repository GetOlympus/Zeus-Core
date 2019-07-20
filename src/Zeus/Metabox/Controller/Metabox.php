<?php

namespace GetOlympus\Zeus\Metabox\Controller;

use GetOlympus\Zeus\Base\Controller\Base;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use GetOlympus\Zeus\Metabox\Exception\MetaboxException;
use GetOlympus\Zeus\Metabox\Implementation\MetaboxImplementation;
use GetOlympus\Zeus\Metabox\Model\MetaboxModel;
use GetOlympus\Zeus\Render\Controller\Render;
use GetOlympus\Zeus\Translate\Controller\Translate;

/**
 * Gets its own post type.
 *
 * @package    OlympusZeusCore
 * @subpackage Metabox\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Metabox extends Base implements MetaboxImplementation
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
     */
    public static function build($title, $fields = [])
    {
        // Get instance
        $metabox = self::getInstance();

        // Check fields
        if (empty($fields)) {
            throw new FieldException(Translate::t('metabox.errors.no_fields'));
        }

        // Define ID
        $id = Helpers::urlize($title);

        // Set details
        $metabox->getModel()->setTitle($title);
        $metabox->getModel()->setFields($fields);

        // Get field
        return $metabox;
    }

    /**
     * Initialization.
     *
     * @param  string  $identifier
     * @param  string  $slug
     */
    public function init($identifier, $slug)
    {
        $this->getModel()->setId($identifier);
        $this->getModel()->setSlug($slug);

        $this->addMetabox();
    }

    /**
     * Add metabox.
     */
    public function addMetabox()
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
     * @return integer|null
     */
    public function callback($post, $args)
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
