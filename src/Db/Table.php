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
use Bluz\Proxy\Cache;
use Bluz\Proxy\Db as DbProxy;

/**
 * Table
 *
 * Example of Users\Table
 *     namespace Application\Users;
 *     class Table extends \Bluz\Db\Table
 *     {
 *        protected $table = 'users';
 *        protected $primary = array('id');
 *     }
 *
 *     $userRows = \Application\Users\Table::find(1,2,3,4,5);
 *     foreach ($userRows as $userRow) {
 *        $userRow -> description = 'In first 5';
 *        $userRow -> save();
 *     }
 *
 * @package  Bluz\Db
 * @link     https://github.com/bluzphp/framework/wiki/Db-Table
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 17:32
 */
abstract class Table
{
    /**
     * @var string The table name
     */
    protected $table;

    /**
     * @var array Table columns
     */
    protected $columns = [];

    /**
     * @var string Default SQL query for select
     */
    protected $select = "";

    /**
     * @var array The primary key column or columns (only as array).
     */
    protected $primary;

    /**
     * @var string The sequence name, required for PostgreSQL
     */
    protected $sequence;

    /**
     * @var string Row class name
     */
    protected $rowClass;

    /**
     * Create and initialize Table instance
     * @return Table
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
            $this->select = "SELECT * ".
                "FROM " . DbProxy::quoteIdentifier($this->table);
        }

        $this->init();
    }

    /**
     * Initialization hook.
     * Subclasses may override this method.
     */
    public function init()
    {
    }

    /**
     * Get Table instance
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
     * Set select query
     * @param $select
     * @return Table
     */
    public function setSelectQuery($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Get select query
     * @return string
     */
    public function getSelectQuery()
    {
        return $this->select;
    }

    /**
     * Get primary key(s)
     * @throws InvalidPrimaryKeyException if primary key was not set or has wrong format
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
     * Get table name
     * @return string
     */
    public function getName()
    {
        return $this->table;
    }

    /**
     * Return information about tables columns
     * @return array
     */
    public function getColumns()
    {
        if (empty($this->columns)) {
            $columns = Cache::get('table:columns:'. $this->table);
            if (!$columns) {
                $connect = DbProxy::getOption('connect');

                $columns = DbProxy::fetchColumn(
                    '
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = ?
                      AND TABLE_NAME = ?',
                    [$connect['name'], $this->getName()]
                );
                Cache::set('table:columns:'. $this->table, $columns);
                Cache::addTag('table:columns:'. $this->table, 'db');
            }
            $this->columns = $columns;

        }
        return $this->columns;
    }

    /**
     * Filter columns for insert/update queries by table columns definition
     * @param $data
     * @return array
     */
    public static function filterColumns($data)
    {
        $self = static::getInstance();
        return array_intersect_key($data, array_flip($self->getColumns()));
    }

    /**
     * Fetching rows by SQL query
     * @param  string $sql query options.
     * @param  array $params
     * @return array of rows results in FETCH_CLASS mode
     */
    public static function fetch($sql, $params = array())
    {
        $self = static::getInstance();
        return DbProxy::fetchObjects($sql, $params, $self->rowClass);
    }

    /**
     * Fetch all rows from table
     * Be carefully with this method, can be very slow
     *
     * @return array of rows results in FETCH_CLASS mode
     */
    public static function fetchAll()
    {
        $self = static::getInstance();
        return DbProxy::fetchObjects($self->select, [], $self->rowClass);
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
     * The find() method always returns a array
     *
     * Row by primary key, return array
     *     Table::find(123);
     *
     * Row by compound primary key, return array
     *     Table::find([123, 'abc']);
     *
     * Multiple rows by primary key
     *     Table::find(123, 234, 345);
     *
     * Multiple rows by compound primary key
     *     Table::find([123, 'abc'], [234, 'def'], [345, 'ghi'])
     *
     * @param mixed $key,... The value(s) of the primary keys.
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     * @return array
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
                    "Please check " . get_called_class() . " initialization or usage.\n" .
                    "Settings described at https://github.com/bluzphp/framework/wiki/Db-Table"
                );
            }

            if (count($keyValues) > count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Too many columns for the primary key.\n" .
                    "Please check " . get_called_class() . " initialization or usage.\n" .
                    "Settings described at https://github.com/bluzphp/framework/wiki/Db-Table"
                );
            }

            if (array_keys($keyValues)[0] === 0) {
                // for numerical array
                $whereList[] = array_combine($keyNames, $keyValues);
            } else {
                // for assoc array
                $whereList[] = $keyValues;
            }
        }
        return call_user_func_array(array($self, 'findWhere'), $whereList);
    }

    /**
     * Find row by primary key
     * @param mixed $primaryKey
     * @return Row
     */
    public static function findRow($primaryKey)
    {
        if (!$primaryKey) {
            return null;
        }
        $self = static::getInstance();
        $result = call_user_func(array($self, 'find'), $primaryKey);
        return current($result);
    }

    /**
     * Find rows by WHERE
     *     // WHERE alias = 'foo'
     *     Table::findWhere(['alias'=>'foo']);
     *     // WHERE alias = 'foo' OR 'alias' = 'bar'
     *     Table::findWhere(['alias'=>'foo'], ['alias'=>'bar']);
     *     // WHERE (alias = 'foo' AND userId = 2) OR ('alias' = 'bar' AND userId = 4)
     *     Table::findWhere(['alias'=>'foo', 'userId'=> 2], ['alias'=>'foo', 'userId'=>4]);
     *     // WHERE alias IN ('foo', 'bar')
     *     Table::findWhere(['alias'=> ['foo', 'bar']]);
     *
     * @throws \InvalidArgumentException
     * @throws Exception\DbException
     * @return array
     */
    public static function findWhere()
    {
        $self = static::getInstance();
        $whereList = func_get_args();

        $whereClause = null;
        $whereParams = array();

        if (sizeof($whereList) == 2 && is_string($whereList[0])) {
            $whereClause = $whereList[0];
            $whereParams = (array)$whereList[1];
        } elseif (sizeof($whereList)) {
            $whereOrTerms = array();
            foreach ($whereList as $keyValueSets) {
                $whereAndTerms = array();
                foreach ($keyValueSets as $keyName => $keyValue) {
                    if (is_array($keyValue)) {
                        $keyValue = array_map(
                            function ($value) use ($self) {
                                return DbProxy::quote($value);
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
        } elseif (!sizeof($whereList)) {
            throw new DbException(
                "Method `Table::findWhere()` can't return all records from table,\n".
                "please use `Table::fetchAll()` instead"
            );
        }
        return $self->fetch($self->select . ' WHERE ' . $whereClause, $whereParams);
    }

    /**
     * Find row by where condition
     * @param array $whereList
     * @return Row
     */
    public static function findRowWhere($whereList)
    {
        $self = static::getInstance();
        $result = call_user_func(array($self, 'findWhere'), $whereList);
        return current($result);
    }

    /**
     * Prepare array for WHERE or SET statements
     * @param $where
     * @throws \Bluz\Common\Exception\ConfigurationException
     * @return array
     */
    private static function prepareStatement($where)
    {
        $keys = array_keys($where);
        foreach ($keys as &$key) {
            $key = DbProxy::quoteIdentifier($key) . ' = ?';
        }
        return $keys;
    }

    /**
     * Prepare Db\Query\Select for current table:
     *  - predefine "select" section as "*" from current table
     *  - predefine "from" section as current table name and first letter as alias
     *  - predefine fetch type
     *
     *     // use default select "*"
     *     $select = Users\Table::select();
     *     $arrUsers = $select->where('u.id = ?', $id)
     *         ->execute();
     *
     *     // setup custom select "u.id, u.login"
     *     $select = Users\Table::select();
     *     $arrUsers = $select->select('u.id, u.login')
     *         ->where('u.id = ?', $id)
     *         ->execute();
     *
     * @return Query\Select
     */
    public static function select()
    {
        $self = static::getInstance();

        $select = new Query\Select();
        $select->select($self->table.'.*')
            ->from($self->table, $self->table)
            ->setFetchType($self->rowClass);

        return $select;
    }

    /**
     * Create Row instance
     * @param array $data
     * @return Row
     */
    public static function create(array $data = [])
    {
        $rowClass = static::getInstance()->rowClass;
        /** @var Row $row */
        $row = new $rowClass($data);
        $row->setTable(static::getInstance());
        return $row;
    }

    /**
     * Insert new record to table and return last insert Id
     *
     *     Table::insert(['login' => 'Man', 'email' => 'man@example.com'])
     *
     * @param  array $data Column-value pairs
     * @throws Exception\DbException
     * @return string|null Primary key or null
     */
    public static function insert(array $data)
    {
        $self = static::getInstance();

        $data = $self->filterColumns($data);

        if (!sizeof($data)) {
            throw new DbException(
                "Invalid field names of table `{$self->table}`. Please check use of `insert()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->table);

        $sql = "INSERT INTO $table SET " . join(',', self::prepareStatement($data));
        $result = DbProxy::query($sql, array_values($data));
        if (!$result) {
            return null;
        }

        /**
         * If a sequence name was not specified for the name parameter, PDO::lastInsertId()
         * returns a string representing the row ID of the last row that was inserted into the database.
         *
         * If a sequence name was specified for the name parameter, PDO::lastInsertId()
         * returns a string representing the last value retrieved from the specified sequence object.
         *
         * If the PDO driver does not support this capability, PDO::lastInsertId() triggers an IM001 SQLSTATE.
         */
        return DbProxy::handler()->lastInsertId($self->sequence);
    }

    /**
     * Updates existing rows
     *
     *     Table::insert(['login' => 'Man', 'email' => 'man@domain.com'], ['id' => 42])
     *
     * @param  array $data Column-value pairs.
     * @param  array $where An array of SQL WHERE clause(s)
     * @throws Exception\DbException
     * @return integer The number of rows updated
     */
    public static function update(array $data, array $where)
    {
        if (!sizeof($where)) {
            throw new DbException(
                "Method `Table::update()` can't update all records in table,\n".
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $self = static::getInstance();

        $data = $self->filterColumns($data);

        $where = $self->filterColumns($where);

        if (!sizeof($data) or !sizeof($where)) {
            throw new DbException(
                "Invalid field names of table `{$self->table}`. Please check use of `update()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->table);

        $sql = "UPDATE $table"
            . " SET " . join(',', self::prepareStatement($data))
            . " WHERE " . join(' AND ', self::prepareStatement($where));

        return DbProxy::query($sql, array_merge(array_values($data), array_values($where)));
    }

    /**
     * Deletes existing rows
     *
     *     Table::delete(['login' => 'Man'])
     *
     * @param  array $where An array of SQL WHERE clause(s)
     * @throws Exception\DbException
     * @return integer The number of rows deleted
     */
    public static function delete(array $where)
    {
        if (!sizeof($where)) {
            throw new DbException(
                "Method `Table::delete()` can't delete all records in table,\n".
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $self = static::getInstance();

        $where = $self->filterColumns($where);

        if (!sizeof($where)) {
            throw new DbException(
                "Invalid field names of table `{$self->table}`. Please check use of `delete()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->table);

        $sql = "DELETE FROM $table"
            . " WHERE " . join(' AND ', self::prepareStatement($where));
        return DbProxy::query($sql, array_values($where));
    }
}
