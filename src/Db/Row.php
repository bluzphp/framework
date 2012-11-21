<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Db;

use Bluz\Db\DbException;

/**
 * Row
 *
 * @category Bluz
 * @package  Db
 * @example
 * <code>
 * namespace Application\Users;
 * class Row extends \Bluz\Db\Row
 * {
 *    public function preInsert()
 *    {
 *        $this->created = gmdate('Y-m-d H:i:s');
 *    }
 *
 *    public function preUpdate()
 *    {
 *        $this->updated = gmdate('Y-m-d H:i:s');
 *    }
 * }
 *
 * $userRow = new \Application\Users\Row();
 * $userRow -> login = 'username';
 * $userRow -> save();
 * </code>
 *
 * @author   Anton Shevchuk
 * @created  07.07.11 19:47
 */
class Row
{
    use \Bluz\Package;

    /**
     * Table class or instance.
     *
     * @var Table
     */
    protected $table = null;

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
     * Tracks columns where data has been updated. Allows more specific insert and
     * update operations.
     *
     * @var array
     */
    protected $modified = array();

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
        // clean modified flags data if setup from PDO
        $this->modified = array();

        // original cleaner data
        $this->clean = $this->toArray();

        // not clean data, but not modified
        if (sizeof($data)) {
            $this->setFromArray($data);
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
        $value = null;
        if (isset($this->data[$columnName])) {
            $value = $this->data[$columnName];
        }
        return $value;
    }

    /**
     * Sleep
     * @return array
     */
    public function __sleep()
    {
        return array_merge(
            array_keys($this->toArray()), // public data
            array('primary', 'data', 'clean' ,'modified')
        );
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     * @throws DbException
     */
    public function __set($columnName, $value)
    {
        if (strpos($columnName, '__') === 0) {
            list($tableName, $columnName) = preg_split('/_/', substr($columnName, 2), 2);
            $tableName = ucfirst(strtolower($tableName));

            if (!empty($tableName) && !empty($columnName)) {
                if (!isset($this->relationsData[$tableName])) {
                    $this->relationsData[$tableName] = array();
                }
                $this->relationsData[$tableName][$columnName] = $value;
            }
        } else {
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            if (!isset($this->data[$columnName])
                || $this->data[$columnName] != $value) {
                $this->data[$columnName] = $value;
            }
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
        /**
         * If the primary key is empty, this is an INSERT of a new row.
         * Otherwise check primary key updated or not, if it changed - INSERT
         * otherwise UPDATE
         */
        if (!sizeof(array_filter($this->getPrimaryKey()))) {
            return $this->doInsert();
        } elseif (sizeof(array_intersect_key(
                    array_diff_assoc($this->clean, $this->toArray()),
                    $this->getPrimaryKey()
                ))) {
            return $this->doInsert();
        } else {
            return $this->doUpdate();
        }
    }

    /**
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function doInsert()
    {
        /**
         * Run pre-INSERT logic
         */
        $this->preInsert();

        /**
         * Execute the INSERT (this may throw an exception)
         */
        $data = $this->toArray();

        $primaryKey = $this->getTable()->insert($data);

        /**
         * Normalize the result to an array indexed by primary key column(s).
         * The table insert() method may return a scalar.
         * TODO: check scalar!!!
         */
        if (is_array($primaryKey)) {
            $newPrimaryKey = $primaryKey;
        } else {
            //ZF-6167 Use tempPrimaryKey temporary to avoid that zend encoding fails.
            $tempPrimaryKey = $this->getTable()->getPrimaryKey();
            $newPrimaryKey = array(current($tempPrimaryKey) => $primaryKey);
        }

        /**
         * Save the new primary key value in object. The primary key may have
         * been generated by a sequence or auto-increment mechanism, and this
         * merge should be done before the postInsert() method is run, so the
         * new values are available for logging, etc.
         */
        $this->setFromArray($newPrimaryKey);

        /**
         * Run post-INSERT logic
         */
        $this->postInsert();

        /**
         * Update the "clean" to reflect that the data has been inserted.
         */
        $this->clean = $this->toArray();
        $this->modified = array();

        return $primaryKey;
    }

    /**
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function doUpdate()
    {
        /**
         * Run pre-UPDATE logic
         */
        $this->preUpdate();

        /**
         * Compare the data to the modified fields array to discover
         * which columns have been changed.
         */
        $diffData = array_diff($this->toArray(), $this->clean);

        /**
         * Execute the UPDATE (this may throw an exception)
         * Do this only if data values were changed.
         * Use the $diffData variable, so the UPDATE statement
         * includes SET terms only for data values that changed.
         */
        if (count($diffData) > 0) {
            $this->getTable()->update($diffData, $this->getPrimaryKey());
        }


        /**
         * Run post-UPDATE logic.  Do this before the _refresh()
         * so the _postUpdate() function can tell the difference
         * between changed data and clean (pre-changed) data.
         */
        $this->postUpdate();

        /**
         * Refresh the data just in case triggers in the RDBMS changed
         * any columns.  Also this resets the "clean".
         */
        $this->clean = $this->toArray();
        $this->modified = array();

        /**
         * Return the primary key value(s) as an array
         * if the key is compound or a scalar if the key
         * is a scalar.
         */
        $primaryKey = $this->getPrimaryKey();
        if (count($primaryKey) == 1) {
            return current($primaryKey);
        }

        return $primaryKey;
    }

    /**
     * Deletes existing rows.
     *
     * @return int The number of rows deleted.
     */
    public function delete()
    {
        /**
         * Execute pre-DELETE logic
         */
        $this->preDelete();

        /**
         * Execute the DELETE (this may throw an exception)
         */
        $result = $this->getTable()->delete($this->getPrimaryKey());

        /**
         * Execute post-DELETE logic
         */
        $this->postDelete();

        /**
         * Reset all fields to null to indicate that the row is not there
         */
        $emptyData = array_combine(
            array_keys($this->toArray()),
            array_fill(0, count($this->toArray()), null)
        );

        $this->setFromArray($emptyData);
        return $result;
    }


    /**
     * Retrieves an associative array of primary keys.
     *
     * @throws InvalidPrimaryKeyException
     * @return array
     */
    protected function getPrimaryKey()
    {
        $primary = array_flip($this->getTable()->getPrimaryKey());

        $array = array_intersect_key($this->toArray(), $primary);

        if (count($primary) != count($array)) {
            throw new InvalidPrimaryKeyException(
                "The specified Table '" . get_class($this->table)
                . "' does not have the same primary key as the Row"
            );
        }
        return $array;
    }


    /**
     * Refreshes properties from the database.
     *
     * @return void
     */
    public function refresh()
    {
        $this->setFromArray($this->clean);
        $this->modified = array();
    }

    /**
     * Pre insert hook
     * @return void
     */
    protected function preInsert()
    {
    }

    /**
     * Allows post-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function postInsert()
    {
    }

    /**
     * Pre update hook
     * @return void
     */
    protected function preUpdate()
    {
    }

    /**
     * Allows post-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function postUpdate()
    {
    }

    /**
     * Pre delete hook
     * @return void
     */
    protected function preDelete()
    {
    }

    /**
     * Allows post-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function postDelete()
    {
    }

    /**
     * Returns the table object, or null if this is disconnected row
     *
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
            $classTable = substr($classRow, 0, strrpos($classRow, '\\', 1)+1) . 'Table';
        }

        try {
            if (class_exists($classTable)) {
                if ($table = call_user_func(array($classTable, 'getInstance'))) {
                    $this->table = $table;
                    return $this->table;
                } else {
                    throw new DbException('"'.$classTable.'" is invalid');
                }
            } else {
                throw new DbException('"'.$classTable.'" not found');
            }
        } catch (\Exception $e) {
            throw new TableNotFoundException('Can\'t found table class with message: '.$e->getMessage());
        }

    }

    /**
     * getRelation
     *
     * @param string $tableName
     * @throws RelationNotFoundException
     * @return \Bluz\Db\Row
     */
    public function getRelation($tableName)
    {
        $tableName = ucfirst(strtolower($tableName));
        if (isset($this->relations[$tableName])) {
            return $this->relations[$tableName];
        } elseif (!isset($this->relationsData[$tableName])) {
            throw new RelationNotFoundException(
                'Can\'t found relation for "'.$tableName.'"'
            );
        }
        $currentClass = get_class($this);
        $classRow = substr($currentClass, 0, strrpos($currentClass, '\\'));
        $classRow = substr($currentClass, 0, strrpos($classRow, '\\'));
        $classRow = $classRow .'\\'.$tableName.'\\Row';

        $this->relations[$tableName] = new $classRow($this->relationsData[$tableName]);

        return $this->relations[$tableName];
    }

    /**
     * setRelation
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
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return getProperties($this);
    }

    /**
     * Sets all data in the row from an array.
     *
     * @param  array $data
     * @return Row Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        $data = array_intersect_key($data, $this->toArray());

        foreach ($data as $columnName => $value) {
            $this->$columnName = $value;
        }
        return $this;
    }
}

/**
 * Workaround for PHP 5.4
 *
 * @param $obj
 * @return array
 */
function getProperties($obj) {
    return get_object_vars($obj);
}