<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Crud;

use Bluz\Application\Exception\NotImplementedException;
use Bluz\Db\Row;

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
     * @var array Fields for action
     */
    protected $fields = [];

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
     *
     * @return mixed
     * @throws NotImplementedException
     */
    public function readOne($primary)
    {
        throw new NotImplementedException;
    }

    /**
     * Get collection of items
     *
     * @param  integer $offset
     * @param  integer $limit
     * @param  array   $params
     * @param  null    $total
     *
     * @return mixed
     * @throws NotImplementedException
     */
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, $params = [], &$total = null)
    {
        throw new NotImplementedException;
    }

    /**
     * Create new item
     *
     * @param  array $data
     *
     * @return mixed
     * @throws NotImplementedException
     */
    public function createOne($data)
    {
        throw new NotImplementedException;
    }

    /**
     * Create items
     *
     * @param  array $data
     *
     * @return mixed
     * @throws NotImplementedException
     */
    public function createSet($data)
    {
        throw new NotImplementedException;
    }

    /**
     * Update item
     *
     * @param  mixed $primary
     * @param  array $data
     *
     * @return integer
     * @throws NotImplementedException
     */
    public function updateOne($primary, $data)
    {
        throw new NotImplementedException;
    }

    /**
     * Update items
     *
     * @param  array $data
     *
     * @return integer
     * @throws NotImplementedException
     */
    public function updateSet($data)
    {
        throw new NotImplementedException;
    }

    /**
     * Delete item
     *
     * @param  mixed $primary
     *
     * @return integer
     * @throws NotImplementedException
     */
    public function deleteOne($primary)
    {
        throw new NotImplementedException;
    }

    /**
     * Delete items
     *
     * @param  array $data
     *
     * @return integer
     * @throws NotImplementedException
     */
    public function deleteSet($data)
    {
        throw new NotImplementedException;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Setup data filters
     *
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Filter input Fields
     *
     * @param array $data Request
     *
     * @return array
     */
    protected function filterData($data) : array
    {
        if (empty($this->getFields())) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->getFields()));
    }

    /**
     * Filter output Row
     *
     * @param Row $row from database
     *
     * @return Row
     */
    protected function filterRow($row)
    {
        if (empty($this->getFields())) {
            return $row;
        }
        $fields = array_keys($row->toArray());
        $toDelete = array_diff($fields, $this->getFields());

        foreach ($toDelete as $field) {
            unset($row->$field);
        }
        return $row;
    }
}
