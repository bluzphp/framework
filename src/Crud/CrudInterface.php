<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Crud;

use Bluz\Http\Exception\NotImplementedException;

/**
 * Crud
 *
 * @package  Bluz\Crud
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Crud
 */
interface CrudInterface
{
    /**
     * Default limit for READ SET of elements
     */
    public const DEFAULT_LIMIT = 10;

    /**
     * Get item by primary key(s)
     *
     * @param mixed $primary
     *
     * @return mixed
     */
    public function readOne($primary);

    /**
     * Get collection of items
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     *
     * @return array[Row[], integer]
     */
    public function readSet(int $offset = 0, int $limit = self::DEFAULT_LIMIT, array $params = []);

    /**
     * Create new item
     *
     * @param array $data
     *
     * @return mixed
     */
    public function createOne(array $data);

    /**
     * Create items
     *
     * @param array $data
     *
     * @return mixed
     */
    public function createSet(array $data);

    /**
     * Update item
     *
     * @param mixed $primary
     * @param array $data
     *
     * @return integer
     */
    public function updateOne($primary, array $data);

    /**
     * Update items
     *
     * @param array $data
     *
     * @return integer
     */
    public function updateSet(array $data);

    /**
     * Delete item
     *
     * @param mixed $primary
     *
     * @return integer
     */
    public function deleteOne($primary);

    /**
     * Delete items
     *
     * @param array $data
     *
     * @return integer
     */
    public function deleteSet(array $data);
}
