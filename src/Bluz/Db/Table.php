<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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

use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;

/**
 * Table
 *
 * @category Bluz
 * @package  Db
 *
 * <pre>
 * <code>
 * namespace Application\Users;
 * class Table extends \Bluz\Db\Table
 * {
 *    protected $table = 'users';
 *    protected $primary = array('id');
 * }
 *
 * $userRows = \Application\Users\Table::find(1,2,3,4,5);
 * foreach ($userRows as $userRow) {
 *    $userRow -> description = 'In first 5';
 *    $userRow -> save();
 * }
 *
 * </code>
 * </pre>
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 17:32
 */
abstract class Table
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Table columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Default SQL query for select
     *
     * @var string
     */
    protected $select = "";

    /**
     * The primary key column or columns.
     * Should be declared as an array.
     *
     * @var array
     */
    protected $primary = null;

    /**
     * @var Db
     */
    protected $adapter = null;

    /**
     * @var string
     */
    protected $rowClass = null;

    /**
     * __construct
     *
     * @return \Bluz\Db\Table
     */
    private function __construct()
    {
        $tableClass = get_called_class();

        // autodetect row class
        if (!$this->rowClass) {
            $rowClass = substr($tableClass, 0, strrpos($tableClass, '\\', 1) + 1);
            $this->rowClass = $rowClass . 'Row';
        }

        // autodetect table name - camelCase to uppercase
        if (!$this->table) {
            $tableClass = substr($tableClass, strpos($tableClass, '\\') + 1);
            $tableClass = substr($tableClass, 0, strpos($tableClass, '\\', 2));

            $table = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $tableClass);
            $this->table = strtolower($table);
        }

        // setup default select query
        if (empty($this->select)) {
            $this->select = "SELECT * FROM ". $this->table;
        }

        $this->init();
    }

    /**
     * Init
     */
    public function init()
    {
    }

    /**
     * getInstance
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
     * Sets a DB adapter.
     *
     * @param Db $adapter DB adapter for table to use
     * @return Table
     * @throws DbException if default DB adapter not initiated
     *                     on \Bluz\Db::$adapter.
     */
    public function setAdapter($adapter = null)
    {
        if (null == $adapter) {
            $this->adapter = Db::getDefaultAdapter();
        }
        return $this;
    }

    /**
     * Gets a DB adapter.
     *
     * @return Db
     */
    public function getAdapter()
    {
        if (!$this->adapter) {
            $this->setAdapter();
        }
        return $this->adapter;
    }

    /**
     * set select query
     *
     * @param $select
     * @return Table
     */
    public function setSelectQuery($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * get select query
     *
     * @return Table
     */
    public function getSelectQuery()
    {
        return $this->select;
    }

    /**
     * get primary key(s)
     *
     * @throws InvalidPrimaryKeyException if primary key was not set or has
     *                                    wrong format
     * @return array
     */
    public function getPrimaryKey()
    {
        if (!is_array($this->primary)) {
            throw new InvalidPrimaryKeyException("The primary key must be set as an array");
        }
        return $this->primary;
    }

    /**
     * getTable
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Return information about tables columns
     *
     * @todo Cache it please
     * @return array
     */
    public function getColumns()
    {
        if (empty($this->columns)) {
            $connect = $this->getAdapter()->getOption('connect');
            $dbName = $connect['name'];

            $this->columns = $this->getAdapter()->fetchColumn(
                '
                SELECT `column_name`
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE `table_schema` = ?
                  AND `table_name` = ?',
                [$dbName, $this->getTableName()]
            );
        }
        return $this->columns;
    }

    /**
     * Support method for fetching rows.
     *
     * @param  string $sql  query options.
     * @param  array $params
     * @return Rowset An array containing the row results in FETCH_ASSOC mode.
     */
    protected static function fetch($sql, $params = array())
    {
        $self = static::getInstance();
        $data = $self->getAdapter()->fetchObjects($sql, $params, $self->rowClass);
        return new Rowset($data);
    }

    /**
     * Fetches rows by primary key.  The argument specifies one or more primary
     * key value(s).  To find multiple rows by primary key, the argument must
     * be an array.
     *
     * This method accepts a variable number of arguments.  If the table has a
     * multi-column primary key, the number of arguments must be the same as
     * the number of columns in the primary key.  To find multiple rows in a
     * table with a multi-column primary key, each argument must be an array
     * with the same number of elements.
     *
     * The find() method always returns a Rowset object, even if only one row
     * was found.
     *
     * <code>
     * // row by primary key
     * // return rowset
     * Table::find(123);
     * // row by compound primary key
     * // return rowset
     * Table::find([123, 'abc']);
     *
     * // multiple rows by primary key
     * Table::find(123, 234, 345);
     * // multiple rows by compound primary key
     * Table::find([123, 'abc'], [234, 'def'], [345, 'ghi'])
     * </code>
     *
     * @internal param mixed $key The value(s) of the primary keys.
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     * @return Rowset Row(s) matching the criteria.
     */
    public static function find()
    {
        $self = static::getInstance();

        $args = func_get_args();
        $keyNames = array_values((array)$self->primary);

        $whereList = array();
        foreach ($args as $keyValues) {
            $keyValues = (array)$keyValues;
            if (count($keyValues) < count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Too few columns for the primary key.\n" .
                    "Please check " . get_class($self) . " initialization or usage.\n" .
                    "Settings described at https://github.com/bluzphp/framework/wiki/Db-Table"
                );
            }

            if (count($keyValues) > count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Too many columns for the primary key.\n" .
                    "Please check " . get_class($self) . " initialization or usage.\n" .
                    "Settings described at https://github.com/bluzphp/framework/wiki/Db-Table"
                );
            }
            $whereList[] = array_combine($keyNames, $keyValues);
        }

        return call_user_func_array(array($self, 'findWhere'), $whereList);
    }

    /**
     * Find row
     *
     * @todo add LIMIT 1 for retrieve only one row
     * @param mixed $primaryKey
     * @return Row
     */
    public static function findRow($primaryKey)
    {
        if (!$primaryKey) {
            return null;
        }
        $self = static::getInstance();
        return call_user_func(array($self, 'find'), $primaryKey)->current();
    }

    /**
     * <code>
     * // WHERE alias = 'foo'
     * Table::findWhere(['alias'=>'foo']);
     * // WHERE alias = 'foo' OR 'alias' = 'bar'
     * Table::findWhere(['alias'=>'foo'], ['alias'=>'bar']);
     * // WHERE (alias = 'foo' AND userId = 2) OR ('alias' = 'bar' AND userId = 4)
     * Table::findWhere(['alias'=>'foo', 'userId'=> 2], ['alias'=>'foo', 'userId'=>4]);
     * // WHERE alias IN ('foo', 'bar')
     * Table::findWhere(['alias'=> ['foo', 'bar']]);
     * </code>
     * @throws \InvalidArgumentException
     * @return Rowset Row(s) matching the criteria.
     */
    public static function findWhere()
    {
        $self = static::getInstance();
        $whereList = func_get_args();

        $whereClause = null;
        $whereParams = array();

        if (count($whereList) == 2 && is_string($whereList[0])) {
            $whereClause = $whereList[0];
            $whereParams = $whereList[1];
        } elseif (count($whereList)) {
            $whereOrTerms = array();
            foreach ($whereList as $keyValueSets) {
                $whereAndTerms = array();
                foreach ($keyValueSets as $keyName => $keyValue) {
                    if (is_array($keyValue)) {
                        $keyValue = array_map(
                            function ($value) use ($self) {
                                return $self->getAdapter()->quote($value);
                            },
                            $keyValue
                        );
                        $keyValue = join(',', $keyValue);
                        $whereAndTerms[] = $self->table . '.' . $keyName . ' IN ('.$keyValue.')';
                    } elseif (is_null($keyValue)) {
                        $whereAndTerms[] = $self->table . '.' . $keyName . ' IS NULL';
                    } else {
                        $whereAndTerms[] = $self->table . '.' . $keyName . ' = ?';
                        $whereParams[] = $keyValue;
                    }
                    if (!is_scalar($keyValue) && !is_null($keyValue)) {
                        throw new \InvalidArgumentException(
                            "Wrong arguments of method 'findWhere'.\n" .
                            "Please use syntax described at https://github.com/bluzphp/framework/wiki/Db-Table"
                        );
                    }
                }
                $whereOrTerms[] = '(' . implode(' AND ', $whereAndTerms) . ')';
            }
            $whereClause = '(' . implode(' OR ', $whereOrTerms) . ')';
        }
        return $self->fetch($self->select . ' WHERE ' . $whereClause, $whereParams);
    }

    /**
     * Find row by where condition
     *
     * @todo add LIMIT 1 for retrieve only one row
     * @param array $whereList
     * @return Row
     */
    public static function findRowWhere($whereList)
    {
        $self = static::getInstance();
        return call_user_func(array($self, 'findWhere'), $whereList)->current();
    }

    /**
     * create
     *
     * @param array $data
     * @return Row
     */
    public static function create(array $data = [])
    {
        $rowClass = static::getInstance()->rowClass;
        $row = new $rowClass($data);
        return $row;
    }

    /**
     * Insert new rows
     *
     * @param  array $data  Column-value pairs
     * @return integer The number of rows inserted
     */
    protected function insert(array $data)
    {
        $sql = "INSERT INTO `{$this->table}` SET `" . join('` = ?,`', array_keys($data)) . "` = ?";
        $result = $this->getAdapter()->query($sql, array_values($data));
        if ($result) {
            return $this->getAdapter()->handler()->lastInsertId();
        }
        return $result;
    }

    /**
     * Updates existing rows
     *
     * @param  array $data  Column-value pairs.
     * @param  array $where An array of SQL WHERE clause(s)
     * @return integer The number of rows updated
     */
    protected function update(array $data, $where)
    {
        $sql = "UPDATE `{$this->table}`"
            . " SET `" . join('` = ?,`', array_keys($data)) . "` = ?"
            . " WHERE `" . join('` = ? AND `', array_keys($where)) . "` = ?";

        return $this->getAdapter()->query($sql, array_merge(array_values($data), array_values($where)));
    }

    /**
     * Deletes existing rows
     *
     * @param  array $where An array of SQL WHERE clause(s)
     * @return integer The number of rows deleted
     */
    protected function delete($where)
    {
        $sql = "DELETE FROM `{$this->table}`"
            . " WHERE `" . join('` = ? AND `', array_keys($where)) . "` = ?";
        return $this->getAdapter()->query($sql, array_values($where));
    }
}
