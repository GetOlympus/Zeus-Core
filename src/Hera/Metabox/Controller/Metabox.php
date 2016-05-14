<?php

namespace GetOlympus\Hera\Metabox\Controller;

use GetOlympus\Hera\Metabox\Model\Metabox as MetaboxModel;
use GetOlympus\Hera\Notification\Controller\Notification;
use GetOlympus\Hera\Render\Controller\Render;
use GetOlympus\Hera\Translate\Controller\Translate;

/**
 * Gets its own post type.
 *
 * @package Olympus Hera
 * @subpackage Metabox\Controller
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.1
 *
 */

class Metabox
{
    /**
     * @var MetaboxModel
     */
    protected $metabox;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->metabox = new MetaboxModel();
    }

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $slug
     * @param string $title
     * @param array $args
     */
    public function initialize($identifier, $slug, $title, $args)
    {
        $this->metabox->setId($identifier);
        $this->metabox->setSlug($slug);
        $this->metabox->setTitle($title);
        $this->metabox->setArgs($args);

        $this->addMetabox();
    }

    /**
     * Add metabox.
     */
    protected function addMetabox()
    {
        add_meta_box(
            $this->metabox->getId(),
            $this->metabox->getTitle(),
            [&$this, 'callback'],
            $this->metabox->getSlug(),
            $this->metabox->getContext(),
            $this->metabox->getPriority(),
            $this->metabox->getArgs()
        );
    }

    /**
     * Callback function.
     *
     * @param array $post
     * @param array $args
     * @return int|null
     */
    public function callback($post, $args)
    {
        // If autosave...
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return isset($post->ID) ? $post->ID : null;
        }

        // Get contents
        $content = isset($args['args']['contents']) ? $args['args']['contents'] : [];
        $field = isset($args['args']['field']) ? $args['args']['field'] : '';

        // Check if a type is defined
        if (empty($content) || empty($field) || !isset($args['args']['type'])) {
            Notification::error(Translate::t('metabox.errors.no_type_is_defined'));

            return null;
        }

        // Display field content
        $tpl = $field->render($content, ['post' => $post]);

        // Return post if it is asked
        return isset($post->ID) ? $post->ID : null;
    }
}
