<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Crud;

use Bluz\Application\Exception\NotImplementedException;

/**
 * Crud
 *
 * @package  Bluz\Crud
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Crud
 */
abstract class AbstractCrud
{
    /**
     * Default limit for READ SET of elements
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Get CRUD Instance
     *
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Return primary key signature
     *
     * @return array
     */
    abstract public function getPrimaryKey();

    /**
     * Get item by primary key(s)
     *
     * @param  mixed $primary
     * @return mixed
     * @throws NotImplementedException
     */
    public function readOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * Get collection of items
     *
     * @param  integer $offset
     * @param  integer $limit
     * @param  array $params
     * @param  null $total
     * @return mixed
     * @throws NotImplementedException
     */
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, $params = [], &$total = null)
    {
        throw new NotImplementedException();
    }

    /**
     * Create new item
     *
     * @param  array $data
     * @return mixed
     * @throws NotImplementedException
     */
    public function createOne($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Create items
     *
     * @param  array $data
     * @return mixed
     * @throws NotImplementedException
     */
    public function createSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Update item
     *
     * @param  mixed $primary
     * @param  array $data
     * @return integer
     * @throws NotImplementedException
     */
    public function updateOne($primary, $data)
    {
        throw new NotImplementedException();
    }

    /**
     * Update items
     *
     * @param  array $data
     * @return integer
     * @throws NotImplementedException
     */
    public function updateSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Delete item
     *
     * @param  mixed $primary
     * @return integer
     * @throws NotImplementedException
     */
    public function deleteOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * Delete items
     *
     * @param  array $data
     * @return integer
     * @throws NotImplementedException
     */
    public function deleteSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Get realized methods
     *
     * @return array
     */
    public function getMethods()
    {
        $reflection = new \ReflectionObject($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $available = [];
        $allow = [
            'readOne',
            'readSet',
            'createOne',
            'createSet',
            'updateOne',
            'updateSet',
            'deleteOne',
            'deleteSet',
        ];

        foreach ($methods as $method) {
            $className = $method->getDeclaringClass()->getName();
            $methodName = $method->getName();

            if (__CLASS__ != $className && in_array($methodName, $allow)) {
                $available[] = $methodName;
            }
        }

        return $available;
    }
}
