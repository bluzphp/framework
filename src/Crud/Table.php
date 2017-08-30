<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Crud;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Application\Exception\NotFoundException;
use Bluz\Common\Singleton;
use Bluz\Db;
use Bluz\Db\Row;
use Bluz\Proxy;

/**
 * Crud Table
 *
 * @package  Bluz\Crud
 * @author   AntonShevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Crud-Table
 */
class Table extends AbstractCrud
{
    /**
     * @var \Bluz\Db\Table instance of Db\Table
     */
    protected $table;

    /**
     * Setup Table instance
     *
     * @param  Db\Table $table
     *
     * @return void
     */
    public function setTable(Db\Table $table)
    {
        $this->table = $table;
    }

    /**
     * Return table instance for manipulation
     *
     * @return Db\Table
     * @throws ApplicationException
     */
    public function getTable()
    {
        if (!$this->table) {
            $this->initTable();
        }
        return $this->table;
    }

    /**
     * Init table instance for manipulation
     *
     * @return void
     * @throws ApplicationException
     */
    protected function initTable()
    {
        $tableClass = class_namespace(static::class) . '\\Table';

        // check class initialization
        if (!class_exists($tableClass) || !is_subclass_of($tableClass, Db\Table::class)) {
            throw new ApplicationException('`Table` class is not exists or not initialized');
        }

        /**
         * @var Db\Table $tableClass
         */
        $this->setTable($tableClass::getInstance());
    }

    /**
     * Get primary key
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->getTable()->getPrimaryKey();
    }

    /**
     * Get record from Db or create new object
     *
     * @param  mixed $primary
     *
     * @return Row
     * @throws NotFoundException
     */
    public function readOne($primary)
    {
        if (!$primary) {
            return $this->getTable()::create();
        }

        $row = $this->getTable()::findRow($primary);

        if (!$row) {
            throw new NotFoundException('Record not found');
        }

        $row = $this->filterRow($row);

        return $row;
    }

    /**
     * Get set of records
     *
     * @param int   $offset
     * @param int   $limit
     * @param array $params
     *
     * @return array[Row[], integer]
     * @throws ApplicationException
     */
    public function readSet($offset = 0, $limit = 10, $params = [])
    {
        $select = $this->getTable()::select();

        // select only required fields
        if (count($this->getFields())) {
            $fields = $this->getFields();
            $name = $this->getTable()->getName();
            $fields = array_map(
                function ($field) use ($name) {
                    return $name .'.'. $field;
                },
                $fields
            );
            $select->select(implode(', ', $fields));
        }

        // switch statement for DB type
        $type = Proxy\Db::getOption('connect', 'type');
        switch ($type) {
            case 'mysql':
                $selectPart = $select->getQueryPart('select');
                $selectPart = 'SQL_CALC_FOUND_ROWS ' . current($selectPart);
                $select->select($selectPart);
                $totalSQL = 'SELECT FOUND_ROWS()';
                break;
            case 'pgsql':
            default:
                $selectTotal = clone $select;
                $selectTotal->select('COUNT(*)');
                $totalSQL = $selectTotal->getSql();
                break;
        }

        $select->setLimit($limit);
        $select->setOffset($offset);

        $result = [];
        $total = 0;

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(
            function () use (&$result, &$total, $select, $totalSQL) {
                $result = $select->execute();
                $total = Proxy\Db::fetchOne($totalSQL);
            }
        );

        return [$result, $total];
    }

    /**
     * Create item
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function createOne($data)
    {
        $row = $this->getTable()::create();

        $data = $this->filterData($data);

        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Update item
     *
     * @param  mixed $primary
     * @param  array $data
     *
     * @return integer
     * @throws NotFoundException
     */
    public function updateOne($primary, $data)
    {
        $row = $this->getTable()::findRow($primary);

        if (!$row) {
            throw new NotFoundException('Record not found');
        }

        $data = $this->filterData($data);

        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Delete item
     *
     * @param  mixed $primary
     *
     * @return integer
     * @throws NotFoundException
     */
    public function deleteOne($primary)
    {
        $row = $this->getTable()::findRow($primary);

        if (!$row) {
            throw new NotFoundException('Record not found');
        }
        return $row->delete();
    }
}
