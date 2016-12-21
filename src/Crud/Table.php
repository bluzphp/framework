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

use Bluz\Application\Exception\ApplicationException;
use Bluz\Application\Exception\NotFoundException;
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
     * @return self
     */
    public function setTable(Db\Table $table)
    {
        $this->table = $table;
        return $this;
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
            $crudClass = static::class;
            $tableClass = substr($crudClass, 0, strrpos($crudClass, '\\', 1) + 1) . 'Table';

            // check class initialization
            if (!class_exists($tableClass) || !is_subclass_of($tableClass, '\\Bluz\\Db\\Table')) {
                throw new ApplicationException("`Table` class is not exists or not initialized");
            }

            /**
             * @var Db\Table $tableClass
             */
            $table = $tableClass::getInstance();

            $this->setTable($table);
        }
        return $this->table;
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
     * @return Row
     * @throws NotFoundException
     */
    public function readOne($primary)
    {
        if (!$primary) {
            return $this->getTable()->create();
        }

        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new NotFoundException('Record not found');
        }

        return $row;
    }

    /**
     * Get set of records
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @param int $total
     * @return array|int|mixed
     * @throws ApplicationException
     */
    public function readSet($offset = 0, $limit = 10, $params = [], &$total = null)
    {
        $select = $this->getTable()->select();

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

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(function () use (&$result, &$total, $select, $totalSQL) {
            $result = $select->execute();

            if (!is_null($total)) {
                $total = Proxy\Db::fetchOne($totalSQL);
            }
        });

        return $result;
    }

    /**
     * Create item
     *
     * @param  array $data
     * @return integer
     */
    public function createOne($data)
    {
        $row = $this->getTable()->create();
        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Update item
     *
     * @param  mixed $primary
     * @param  array $data
     * @return integer
     * @throws NotFoundException
     */
    public function updateOne($primary, $data)
    {
        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new NotFoundException("Record not found");
        }

        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Delete item
     *
     * @param  mixed $primary
     * @return integer
     * @throws NotFoundException
     */
    public function deleteOne($primary)
    {
        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new NotFoundException("Record not found");
        }
        return $row->delete();
    }
}
