<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Crud;

use Bluz\Application\Exception\NotImplementedException;

/**
 * Crud
 *
 * @package  Bluz\Crud
 *
 * @author   Anton Shevchuk
 * @created  23.09.13 15:33
 */
abstract class AbstractCrud
{
    /**
     * Default limit for READ SET of elements
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Get CRUD Instance
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
     * @return array
     */
    abstract public function getPrimaryKey();

    /**
     * Get item by primary key(s)
     * @param mixed $primary
     * @throws NotImplementedException
     * @return mixed
     */
    public function readOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * list of items
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @throws NotImplementedException
     * @return mixed
     */
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, $params = array())
    {
        throw new NotImplementedException();
    }

    /**
     * Create new item
     * @param array $data
     * @throws NotImplementedException
     * @return mixed
     */
    public function createOne($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Create items
     * @param array $data
     * @throws NotImplementedException
     * @return mixed
     */
    public function createSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Update item
     * @param mixed $primary
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    public function updateOne($primary, $data)
    {
        throw new NotImplementedException();
    }

    /**
     * Update items
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    public function updateSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * Delete item
     * @param mixed $primary
     * @throws NotImplementedException
     * @return integer
     */
    public function deleteOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * Delete items
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    public function deleteSet($data)
    {
        throw new NotImplementedException();
    }
}
