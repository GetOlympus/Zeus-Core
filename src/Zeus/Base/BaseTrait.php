<?php

namespace GetOlympus\Zeus\Base;

/**
 * Base trait
 *
 * @package    OlympusZeusCore
 * @subpackage Base
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      2.2.0
 *
 */

trait BaseTrait
{
    /**
     * @var mixed
     */
    protected $model;

    /**
     * Retrieve class details.
     *
     * @return array
     */
    protected function getClass() : array
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
     * @return static
     */
    public static function getInstance() : self
    {
        return new static();
    }

    /**
     * Gets the model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
