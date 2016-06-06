<?php

namespace GetOlympus\Hera\Metabox\Controller;

use GetOlympus\Hera\Metabox\Controller\MetaboxInterface;
use GetOlympus\Hera\Metabox\Exception\MetaboxException;
use GetOlympus\Hera\Metabox\Model\MetaboxModel;
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

class Metabox implements MetaboxInterface
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
    public function init($identifier, $slug, $title, $args)
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
    public function addMetabox()
    {
        add_meta_box(
            $this->metabox->getId(),
            $this->metabox->getTitle(),
            [$this, 'callback'],
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

        // Get field
        $field = isset($args['args']['field']) ? $args['args']['field'] : null;

        // Check if a type is defined
        if (!$field || empty($field)) {
            throw new MetaboxException(Translate::t('metabox.errors.no_type_is_defined'));
        }

        // Display field content
        $field->render(['post' => $post]);

        // Return post if it is asked
        return isset($post->ID) ? $post->ID : null;
    }
}
