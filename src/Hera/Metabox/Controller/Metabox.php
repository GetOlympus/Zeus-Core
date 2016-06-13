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
     * Build Metabox component.
     *
     * @param string    $title
     * @param array     $fields
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
        $id = Render::urlize($title);

        // Set details
        $metabox->metabox->setTitle($title);
        $metabox->metabox->setFields($fields);

        // Get field
        return $metabox;
    }

    /**
     * Gets the value of instance.
     *
     * @return Metabox
     */
    public static function getInstance()
    {
        return new static();
    }

    /**
     * Gets the value of metabox.
     *
     * @return MetaboxModel
     */
    public function getMetabox()
    {
        return $this->metabox;
    }

    /**
     * Initialization.
     *
     * @param string $identifier
     * @param string $slug
     */
    public function init($identifier, $slug)
    {
        $this->metabox->setId($identifier);
        $this->metabox->setSlug($slug);

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
            $this->metabox->getFields()
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
        $fields = isset($args['args']) ? $args['args'] : null;

        // Check if fields are defined
        if (!$fields || empty($fields)) {
            throw new MetaboxException(Translate::t('metabox.errors.no_type_is_defined'));
        }

        // Display fields
        foreach ($fields as $field) {
            if (!$field) {
                continue;
            }

            $field->render([], [
                'post' => $post,
                'template' => 'metabox'
            ]);
        }

        // Return post if it is asked
        return isset($post->ID) ? $post->ID : null;
    }
}
