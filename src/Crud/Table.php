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
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Exception\TableNotFoundException;
use Bluz\Http\Exception\NotFoundException;
use Bluz\Db;
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
    use Db\Traits\TableProperty;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     */
    public function getPrimaryKey(): array
    {
        return $this->getTable()->getPrimaryKey();
    }

    /**
     * Get record from Db or create new object
     *
     * @param  mixed $primary
     *
     * @return Db\RowInterface
     * @throws TableNotFoundException
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
     * @throws TableNotFoundException
     */
    public function readSet($offset = 0, $limit = 10, $params = [])
    {
        $select = $this->getTable()::select();

        // select only required fields
        if (\count($this->getFields())) {
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
                $selectPart = $select->getSelect();
                $selectPart[0] = 'SQL_CALC_FOUND_ROWS ' . $selectPart[0];
                $select->select(...$selectPart);
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
     * @throws TableNotFoundException
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
     * @throws TableNotFoundException
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
     * @throws TableNotFoundException
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
