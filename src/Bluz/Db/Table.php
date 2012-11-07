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
 *    static $instance;
 *    protected $table = 'users';
 *    protected $primary = array('id');
 * }
 *
 * $usersTable = new \Application\Users\Table();
 * $userRows = $usersTable -> find(array(1,2,3,4,5));
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
     * The schema name (default null means current schema)
     *
     * @var array
     */
    protected $schema = null;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Default SQL query for select
     *
     * @var string
     */
    protected $select = "";

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
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
     * @throws DbException
     * @return \Bluz\Db\Table
     */
    private function __construct()
    {
        if (!$this->table) {
            throw new DbException("Table information for {".__CLASS__."} is not initialized");
        }

        if (empty($this->select)) {
            $this->select = "SELECT * FROM {$this->table}";
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
    static public function getInstance()
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
     *
     * @return Table
     *
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
     * getRowClass
     *
     * @return string
     */
    public function getRowClass()
    {
        if (!$this->rowClass) {
            $tableClass = get_called_class();
            $this->rowClass = substr($tableClass, 0, strrpos($tableClass, '\\', 1)+1) . 'Row';
        }
        return $this->rowClass;
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
     * $table->find(123);
     * // row by compound primary key
     * $table->find([123, 'abc']);
     *
     * // multiple rows by primary key
     * $table->find(123, 234, 345);
     * // multiple rows by compound primary key
     * $table->find([123, 'abc'], [234, 'def'], [345, 'ghi'])
     * </code>
     *
     * @internal param mixed $key The value(s) of the primary keys.
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     * @return Rowset Row(s) matching the criteria.
     */
    public function find()
    {
        $args = func_get_args();
        $keyNames = array_values((array) $this->primary);

//        var_dump($args, $keyNames);

        $whereList = array();
        foreach ($args as $keyValues) {
            $keyValues = (array) $keyValues;
            if (count($keyValues) < count($keyNames)) {
                throw new InvalidPrimaryKeyException("Too few columns for the primary key");
            }

            if (count($keyValues) > count($keyNames)) {
                throw new InvalidPrimaryKeyException("Too many columns for the primary key");
            }
            $whereList[] = array_combine($keyNames, $keyValues);
        }

        return call_user_func_array(array($this, 'findWhere'), $whereList);
    }

    /**
     * Find row
     *
     * @param $primaryKey
     * @return Row
     */
    public function findRow($primaryKey)
    {
        return call_user_func(array($this, 'find'), $primaryKey)->current();
    }

    /**
     * <code>
     * // WHERE alias = 'foo'
     * $table->findWhere(['alias'=>'foo']);
     * // WHERE alias = 'foo' OR 'alias' = 'bar'
     * $table->findWhere(['alias'=>'foo'], ['alias'=>'bar']);
     * // WHERE (alias = 'foo' AND userId = 2) OR ('alias' = 'bar' AND userId = 4)
     * $table->findWhere(['alias'=>'foo', 'userId'=> 2], ['alias'=>'foo', 'userId'=>4]);
     * </code>
     * @return Rowset Row(s) matching the criteria.
     */
    public function findWhere()
    {
        $whereList = func_get_args();

        $whereClause = null;
        $whereParams = array();
        if (count($whereList)) {
            $whereOrTerms = array();
            foreach ($whereList as $keyValueSets) {
                $whereAndTerms = array();
                foreach ($keyValueSets as $keyName => $keyValue) {
                    $whereAndTerms[] = $this->table . '.' . $keyName . ' = ?';
                    $whereParams[] = $keyValue;
                }
                $whereOrTerms[] = '(' . implode(' AND ', $whereAndTerms) . ')';
            }
            $whereClause = '(' . implode(' OR ', $whereOrTerms) . ')';
        }
        return $this->fetch($this->select .' WHERE '. $whereClause, $whereParams);
    }

    /**
     * Find row by where condition
     *
     * @param array $whereList
     * @return Row
     */
    public function findRowWhere($whereList)
    {
        return call_user_func(array($this, 'findWhere'), $whereList)->current();
    }

    /**
     * Support method for fetching rows.
     *
     * @param  string $sql  query options.
     * @param  array  $params
     * @return array An array containing the row results in FETCH_ASSOC mode.
     */
    protected function fetch($sql, $params = array())
    {
        $data = $this->getAdapter()->fetchObjects($sql, $params, $this->getRowClass());
        return new Rowset(array('table' => $this, 'data' => $data));
    }

    /**
     * Be or not to be?
     *
     * Support method for fetching rows.
     *
     * @return array An array containing the row results in FETCH_ASSOC mode.
     */
//    public function fetchAll()
//    {
//        $data = $this->getAdapter()->fetchObjects($this->select, [], $this->getRowClass());
//        return new Rowset(array('table' => $this, 'data' => $data));
//    }

    /**
     * create
     *
     * @param array $data
     * @return Row
     */
    public function create(array $data = [])
    {
        $rowClass = $this->getRowClass();
        $row = new $rowClass($data);
        return $row;
    }

    /**
     * @TODO: insert/update/delete should use Row Object?!
     */

    /**
     * Insert new rows.
     *
     * @param  array        $data  Column-value pairs.
     * @return int          The number of rows updated.
     */
    public function insert(array $data)
    {
        $table = ($this->schema ? $this->schema . '.' : '') . $this->table;
        return $this->getAdapter()->insert($table, $data);
    }

    /**
     * Updates existing rows.
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        $table = ($this->schema ? $this->schema . '.' : '') . $this->table;
        return $this->getAdapter()->update($table, $data, $where);
    }


    /**
     * Deletes existing rows.
     *
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        $table = ($this->schema ? $this->schema . '.' : '') . $this->table;
        return $this->getAdapter()->delete($table, $where);
    }
}
