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

use Bluz\Application\Exception\NotFoundException;
use Bluz\Common\Options;
use Bluz\Db;
use Bluz\Db\Row;
use Bluz\Request\AbstractRequest;

/**
 * Crud
 *
 * @package  Bluz\Crud
 *
 * @author   AntonShevchuk
 * @created  15.08.12 15:37
 */
class Table extends AbstractCrud
{
    /**
     * @var \Bluz\Db\Table instance
     */
    protected $table;

    /**
     * Setup Table instance
     *
     * @param Db\Table $table
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
     * @throws CrudException
     * @return Db\Table
     */
    public function getTable()
    {
        if (!$this->table) {
            $crudClass = get_called_class();
            $tableClass = substr($crudClass, 0, strrpos($crudClass, '\\', 1) + 1) . 'Table';

            // check class initialization
            if (!is_subclass_of($tableClass, '\Bluz\Db\Table')) {
                throw new CrudException("`Table` class is not exists or not initialized");
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
     * getPrimaryKey
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->getTable()->getPrimaryKey();
    }

    /**
     * CRUD methods here
     */

    /**
     * Get record from Db or create new
     *
     * @param mixed $primary
     * @throws NotFoundException
     * @return Row
     */
    public function readOne($primary)
    {
        if (!$primary) {
            return $this->getTable()->create();
        }
        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new NotFoundException("Record not found");
        }

        return $row;
    }

    /**
     * createOne
     *
     * @param array $data
     * @return integer
     */
    public function createOne($data)
    {
        $this->validate(null, $data);
        $this->validateCreate($data);
        $this->checkErrors();

        $row = $this->getTable()->create();

        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Update Record
     *
     * @param mixed $primary
     * @param array $data
     * @throws NotFoundException
     * @return integer
     */
    public function updateOne($primary, $data)
    {
        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new NotFoundException("Record not found");
        }

        $this->validate($primary, $data);
        $this->validateUpdate($primary, $data);
        $this->checkErrors();

        $row->setFromArray($data);
        return $row->save();
    }

    /**
     * Delete Record
     *
     * @param mixed $primary
     * @throws NotFoundException
     * @return integer
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
