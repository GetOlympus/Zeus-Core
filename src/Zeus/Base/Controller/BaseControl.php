<?php

namespace GetOlympus\Zeus\Base\Controller;

/**
 * Base Control controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.0.2
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

if (!class_exists('WP_Customize_Control')) {
    require_once ABSPATH.'wp-includes'.S.'class-wp-customize-control.php';
}

class BaseControl extends \WP_Customize_Control
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
        return new static(false, false, []);
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
