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
namespace Bluz\Db;

use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Exception\RelationNotFoundException;
use Bluz\Db\Exception\TableNotFoundException;

/**
 * Row
 *
 * Example of Users\Row
 *     namespace Application\Users;
 *     class Row extends \Bluz\Db\Row
 *     {
 *        public function beforeInsert()
 *        {
 *            $this->created = gmdate('Y-m-d H:i:s');
 *        }
 *
 *        public function beforeUpdate()
 *        {
 *            $this->updated = gmdate('Y-m-d H:i:s');
 *        }
 *     }
 *
 *     $userRow = new \Application\Users\Row();
 *     $userRow -> login = 'username';
 *     $userRow -> save();
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 * @created  07.07.11 19:47
 */
class Row implements \JsonSerializable, \ArrayAccess
{
    /**
     * Table class or instance.
     *
     * @var Table
     */
    protected $table;

    /**
     * Primary row key(s).
     *
     * @var array
     */
    protected $primary;

    /**
     * The data for each column in the row (column_name => value).
     * The keys must match the physical names of columns in the
     * table for which this row is defined.
     *
     * @var array
     */
    protected $data = array();

    /**
     * This is set to a copy of $data when the data is fetched from
     * a database, specified as a new tuple in the constructor, or
     * when dirty data is posted to the database with save().
     *
     * @var array
     */
    protected $clean = array();

    /**
     * Relations rows
     *
     * @var array
     */
    protected $relations = array();

    /**
     * Relations data
     *
     * @var array
     */
    protected $relationsData = array();

    /**
     * __construct
     *
     * @param array $data
     * @return \Bluz\Db\Row
     */
    public function __construct($data = array())
    {
        // original cleaner data
        $this->clean = $this->toArray();

        // not clean data, but not modified
        if (sizeof($data)) {
            $this->setFromArray($data);
        }
        $this->afterRead();
    }

    /**
     * Sleep
     *
     * @return array
     */
    public function __sleep()
    {
        return array('primary', 'data', 'clean');
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed $value      The value for the property.
     * @return void
     * @throws DbException
     */
    public function __set($columnName, $value)
    {
        if (strpos($columnName, '__') === 0) {
            // it's just relation data
            list($modelName, $columnName) = preg_split('/_/', substr($columnName, 2), 2);
            if (!empty($modelName) && !empty($columnName)) {
                if (!isset($this->relationsData[$modelName])) {
                    $this->relationsData[$modelName] = array();
                }
                $this->relationsData[$modelName][$columnName] = $value;
            }
        } else {
            $this->data[$columnName] = $value;
        }
    }

    /**
     * Retrieve row field value
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     * @throws DbException if the $columnName is not a column in the row.
     */
    public function __get($columnName)
    {
        if (isset($this->data[$columnName])) {
            return $this->data[$columnName];
        } else {
            return null;
        }
    }

    /**
     * Saves the properties to the database.
     *
     * This performs an intelligent insert/update, and reloads the
     * properties with fresh data from the table on success.
     *
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    public function save()
    {
        $this->beforeSave();
        /**
         * If the primary key is empty, this is an INSERT of a new row.
         * Otherwise check primary key updated or not, if it changed - INSERT
         * otherwise UPDATE
         */
        if (!sizeof(array_filter($this->getPrimaryKey()))) {
            $result = $this->doInsert();
        } elseif (sizeof(array_diff_assoc($this->getPrimaryKey(), $this->clean))) {
            $result = $this->doInsert();
        } else {
            $result = $this->doUpdate();
        }
        $this->afterSave();
        return $result;
    }

    /**
     * Insert row to Db
     *
     * @throws Exception\DbException
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function doInsert()
    {
        /**
         * Run pre-INSERT logic
         */
        $this->beforeInsert();

        /**
         * Execute the INSERT (this may throw an exception)
         */
        $data = $this->toArray();

        $table = $this->getTable();
        $data = $table->filterColumns($data);

        if (!sizeof($data)) {
            throw new DbException("Columns data for table `{$table->getName()}` is missed");
        }

        $primaryKey = $table->insert($data);

        /**
         * Normalize the result to an array indexed by primary key column(s)
         */
        $tempPrimaryKey = $table->getPrimaryKey();
        $newPrimaryKey = array(current($tempPrimaryKey) => $primaryKey);

        /**
         * Save the new primary key value in object. The primary key may have
         * been generated by a sequence or auto-increment mechanism, and this
         * merge should be done before the afterInsert() method is run, so the
         * new values are available for logging, etc.
         */
        $this->setFromArray($newPrimaryKey);

        /**
         * Run post-INSERT logic
         */
        $this->afterInsert();

        /**
         * Update the "clean" to reflect that the data has been inserted.
         */
        $this->clean = $this->toArray();

        return $newPrimaryKey;
    }

    /**
     * Update row
     *
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function doUpdate()
    {
        /**
         * Run pre-UPDATE logic
         */
        $this->beforeUpdate();

        $primaryKey = $this->getPrimaryKey();

        /**
         * Compare the data to the modified fields array to discover
         * which columns have been changed.
         */
        $diffData = array_diff_assoc($this->toArray(), $this->clean);

        $table = $this->getTable();
        $diffData = $table->filterColumns($diffData);

        /**
         * Execute the UPDATE (this may throw an exception)
         * Do this only if data values were changed.
         * Use the $diffData variable, so the UPDATE statement
         * includes SET terms only for data values that changed.
         */
        if (sizeof($diffData) > 0) {
            $result = $table->update($diffData, $primaryKey);
        } else {
            $result = 0;
        }

        /**
         * Run post-UPDATE logic.  Do this before the _refresh()
         * so the _afterUpdate() function can tell the difference
         * between changed data and clean (pre-changed) data.
         */
        $this->afterUpdate();

        /**
         * Refresh the data just in case triggers in the RDBMS changed
         * any columns.  Also this resets the "clean".
         */
        $this->clean = $this->toArray();

        return $result;
    }

    /**
     * Delete existing row
     *
     * @return int The number of rows deleted.
     */
    public function delete()
    {
        /**
         * Execute pre-DELETE logic
         */
        $this->beforeDelete();

        $primaryKey = $this->getPrimaryKey();

        /**
         * Execute the DELETE (this may throw an exception)
         */
        $table = $this->getTable();
        $result = $table->delete($primaryKey);

        /**
         * Execute post-DELETE logic
         */
        $this->afterDelete();

        /**
         * Reset all fields to null to indicate that the row is not there
         */
        foreach ($this->data as &$value) {
            $value = null;
        }
        return $result;
    }

    /**
     * Retrieves an associative array of primary keys, if it exists
     *
     * @throws InvalidPrimaryKeyException
     * @return array
     */
    protected function getPrimaryKey()
    {
        $primary = array_flip($this->getTable()->getPrimaryKey());

        $array = array_intersect_key($this->toArray(), $primary);

        return $array;
    }

    /**
     * Refreshes properties from the database
     * @return void
     */
    public function refresh()
    {
        $this->setFromArray($this->clean);
        $this->afterRead();
    }

    /**
     * After read data from Db
     * @return void
     */
    protected function afterRead()
    {
    }

    /**
     * Before Insert/Update
     * @return void
     */
    protected function beforeSave()
    {
    }

    /**
     * After Insert/Update
     * @return void
     */
    protected function afterSave()
    {
    }

    /**
     * Pre insert hook
     * @return void
     */
    protected function beforeInsert()
    {
    }

    /**
     * Allows post-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function afterInsert()
    {
    }

    /**
     * Pre update hook
     * @return void
     */
    protected function beforeUpdate()
    {
    }

    /**
     * Allows post-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function afterUpdate()
    {
    }

    /**
     * Pre delete hook
     * @return void
     */
    protected function beforeDelete()
    {
    }

    /**
     * Allows post-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function afterDelete()
    {
    }

    /**
     * Returns the table object, or null if this is disconnected row
     *
     * @throws TableNotFoundException
     * @throws DbException
     * @return Table|null
     */
    public function getTable()
    {
        if ($this->table instanceof Table) {
            return $this->table;
        }

        if (is_string($this->table)) {
            $classTable = $this->table;
        } else {
            // try to guess table class
            $classRow = get_class($this);
            /**
             * @var string $classTable is child of \Bluz\Db\Table
             */
            $classTable = substr($classRow, 0, strrpos($classRow, '\\', 1) + 1) . 'Table';
        }

        try {
            if (class_exists($classTable)) {
                if ($table = call_user_func(array($classTable, 'getInstance'))) {
                    $this->table = $table;
                    return $this->table;
                } else {
                    throw new DbException('"' . $classTable . '" is invalid');
                }
            } else {
                throw new DbException('"' . $classTable . '" not found');
            }
        } catch (\Exception $e) {
            throw new TableNotFoundException('Can\'t find table class: ' . $e->getMessage());
        }
    }

    /**
     * Get relation
     *
     * @param string $modelName
     * @throws RelationNotFoundException
     * @return \Bluz\Db\Row
     */
    public function getRelation($modelName)
    {
        if (isset($this->relations[$modelName])) {
            return $this->relations[$modelName];
        } elseif (!isset($this->relationsData[$modelName])) {
            throw new RelationNotFoundException(
                'Can\'t found relation data for model "' . $modelName . '"'
            );
        }
        $currentClass = get_class($this);
        $classRow = substr($currentClass, 0, strrpos($currentClass, '\\'));
        $nameSpace = substr($currentClass, 0, strrpos($classRow, '\\'));
        $classRow = $nameSpace . '\\' . $modelName . '\\Row';

        $this->relations[$modelName] = new $classRow($this->relationsData[$modelName]);

        return $this->relations[$modelName];
    }

    /**
     * Set relation
     *
     * @param Row $row
     * @return Row
     */
    public function setRelation(Row $row)
    {
        $class = get_class($row);
        $this->relations[$class] = $row;
        return $this;
    }

    /**
     * Implement JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Sets all data in the row from an array
     *
     * @param  array $data
     * @return Row Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $columnName => $value) {
            $this->$columnName = $value;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \InvalidArgumentException('Class `Db\Row` not fully support `ArrayAccess`');
        } else {
            $this->__set($offset, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     * @return mixed|string
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
}
