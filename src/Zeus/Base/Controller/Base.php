<?php

namespace GetOlympus\Zeus\Base\Controller;

/**
 * Base controller
 *
 * @package    OlympusZeusCore
 * @subpackage Base\Controller
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.8
 *
 */

class Base
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
     * @return Object
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
