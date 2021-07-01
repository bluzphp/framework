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
     * @param  mixed $primary
     *
     * @return mixed
     */
    public function readOne($primary);

    /**
     * Get collection of items
     *
     * @param  integer $offset
     * @param  integer $limit
     * @param  array   $params
     *
     * @return array[Row[], integer]
     */
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, $params = []);

    /**
     * Create new item
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function createOne($data);

    /**
     * Create items
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function createSet($data);

    /**
     * Update item
     *
     * @param  mixed $primary
     * @param  array $data
     *
     * @return integer
     */
    public function updateOne($primary, $data);

    /**
     * Update items
     *
     * @param  array $data
     *
     * @return integer
     */
    public function updateSet($data);

    /**
     * Delete item
     *
     * @param  mixed $primary
     *
     * @return integer
     */
    public function deleteOne($primary);

    /**
     * Delete items
     *
     * @param  array $data
     *
     * @return integer
     */
    public function deleteSet($data);
}
