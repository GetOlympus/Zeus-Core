<?php

namespace GetOlympus\Zeus\Base\Controller;

/**
 * Base Widget controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.8
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

if (!class_exists('WP_Widget')) {
    require_once ABSPATH.'wp-includes'.S.'class-wp-widget.php';
}

class BaseWidget extends \WP_Widget
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Retrieve class details.
     */
    protected function getClass()
    {
        // Retrieve path to Resources and shortname's class
        $class = new \ReflectionClass(get_class($this));

        // Return a simple array
        return [
            'name'      => $class->getShortName(),
            'path'      => $class->getFileName(),
            'resources' => dirname(dirname($class->getFileName())).S.'Resources',
        ];
    }

    /**
     * Gets the value of instance.
     *
     * @return object
     */
    public static function getInstance()
    {
        return new static();
    }

    /**
     * Gets the model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
