<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Db as DbProxy;

/**
 * Table
 *
 * Example of Users\Table
 * <code>
 *     namespace Application\Users;
 *     class Table extends \Bluz\Db\Table
 *     {
 *        protected $table = 'users';
 *        protected $primary = ['id'];
 *     }
 *
 *     $userRows = \Application\Users\Table::find(1,2,3,4,5);
 *     foreach ($userRows as $userRow) {
 *        $userRow -> description = 'In first 5';
 *        $userRow -> save();
 *     }
 * </code>
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Db-Table
 */
abstract class Table
{
    /**
     * @var string the table name
     */
    protected $name;

    /**
     * @var string the model name
     */
    protected $model;

    /**
     * @var array table columns
     */
    protected $columns = [];

    /**
     * @var string default SQL query for select
     */
    protected $select = '';

    /**
     * @var array the primary key column or columns (only as array).
     */
    protected $primary;

    /**
     * @var string the sequence name, required for PostgreSQL
     */
    protected $sequence;

    /**
     * @var string row class name
     */
    protected $rowClass;

    /**
     * Create and initialize Table instance
     */
    private function __construct()
    {
        $tableClass = static::class;
        $namespace = class_namespace($tableClass);

        // autodetect model name
        if (!$this->model) {
            $this->model = substr($namespace, strrpos($namespace, '\\') + 1);
        }

        // autodetect table name - camelCase to uppercase
        if (!$this->name) {
            $table = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $this->model);
            $this->name = strtolower($table);
        }

        // autodetect row class
        if (!$this->rowClass) {
            $this->rowClass = $namespace . '\\Row';
        }

        // setup default select query
        if (empty($this->select)) {
            $this->select = 'SELECT * ' .
                'FROM ' . DbProxy::quoteIdentifier($this->name);
        }

        Relations::addClassMap($this->model, $tableClass);

        $this->init();
    }

    /**
     * Initialization hook.
     * Subclasses may override this method
     */
    public function init()
    {
    }

    /**
     * Get Table instance
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
     * Set select query
     *
     * @param  string $select SQL query
     *
     * @return Table
     */
    public function setSelectQuery($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Get select query
     *
     * @return string
     */
    public function getSelectQuery()
    {
        return $this->select;
    }

    /**
     * Get primary key(s)
     *
     * @return array
     * @throws InvalidPrimaryKeyException if primary key was not set or has wrong format
     */
    public function getPrimaryKey()
    {
        if (!is_array($this->primary)) {
            throw new InvalidPrimaryKeyException('The primary key must be set as an array');
        }
        return $this->primary;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get model name
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Return information about tables columns
     *
     * @return array
     */
    public static function getColumns()
    {
        $self = static::getInstance();
        if (empty($self->columns)) {
            $cacheKey = "db.table.{$self->name}";
            $columns = Cache::get($cacheKey);
            if (!$columns) {
                $schema = DbProxy::getOption('connect', 'name');

                $columns = DbProxy::fetchColumn(
                    '
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = ?
                      AND TABLE_NAME = ?',
                    [$schema, $self->getName()]
                );
                Cache::set($cacheKey, $columns, Cache::TTL_NO_EXPIRY, ['system', 'db']);
            }
            $self->columns = $columns;
        }
        return $self->columns;
    }

    /**
     * Filter columns for insert/update queries by table columns definition
     *
     * @param  array $data
     *
     * @return array
     */
    public static function filterColumns($data)
    {
        return array_intersect_key($data, array_flip(static::getColumns()));
    }

    /**
     * Fetching rows by SQL query
     *
     * @param  string $sql    SQL query with placeholders
     * @param  array  $params Params for query placeholders
     *
     * @return array of rows results in FETCH_CLASS mode
     */
    public static function fetch($sql, $params = [])
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
     * @param  mixed ...$keys The value(s) of the primary keys.
     *
     * @return array
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     */
    public static function find(...$keys)
    {
        $keyNames = array_values(static::getInstance()->getPrimaryKey());
        $whereList = [];
        foreach ($keys as $keyValues) {
            $keyValues = (array)$keyValues;
            if (count($keyValues) < count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Too few columns for the primary key.\n" .
                    "Please check " . static::class . " initialization or usage.\n" .
                    "Settings described at https://github.com/bluzphp/framework/wiki/Db-Table"
                );
            }

            if (count($keyValues) > count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Too many columns for the primary key.\n" .
                    "Please check " . static::class . " initialization or usage.\n" .
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
        return static::findWhere(...$whereList);
    }

    /**
     * Find row by primary key
     *
     * @param  mixed $primaryKey
     *
     * @return Row
     */
    public static function findRow($primaryKey)
    {
        if (!$primaryKey) {
            return null;
        }
        $result = static::find($primaryKey);
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
     * @param  mixed ...$where
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws Exception\DbException
     */
    public static function findWhere(...$where)
    {
        $self = static::getInstance();

        $whereParams = [];

        if (count($where) === 2 && is_string($where[0])) {
            $whereClause = $where[0];
            $whereParams = (array)$where[1];
        } elseif (count($where)) {
            $whereOrTerms = [];
            foreach ($where as $keyValueSets) {
                $whereAndTerms = [];
                foreach ($keyValueSets as $keyName => $keyValue) {
                    if (is_array($keyValue)) {
                        $keyValue = array_map(
                            function ($value) use ($self) {
                                return DbProxy::quote($value);
                            },
                            $keyValue
                        );
                        $keyValue = implode(',', $keyValue);
                        $whereAndTerms[] = $self->name . '.' . $keyName . ' IN (' . $keyValue . ')';
                    } elseif (is_null($keyValue)) {
                        $whereAndTerms[] = $self->name . '.' . $keyName . ' IS NULL';
                    } else {
                        $whereAndTerms[] = $self->name . '.' . $keyName . ' = ?';
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
        } else {
            throw new DbException(
                "Method `Table::findWhere()` can't return all records from table,\n" .
                "please use `Table::fetchAll()` instead"
            );
        }

        return static::fetch($self->select . ' WHERE ' . $whereClause, $whereParams);
    }

    /**
     * Find row by where condition
     *
     * @param  array $whereList
     *
     * @return Row
     */
    public static function findRowWhere($whereList)
    {
        $result = static::findWhere($whereList);
        return current($result);
    }

    /**
     * Prepare array for WHERE or SET statements
     *
     * @param  array $where
     *
     * @return array
     * @throws \Bluz\Common\Exception\ConfigurationException
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
     * <code>
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
     * </code>
     *
     * @return Query\Select
     */
    public static function select()
    {
        $self = static::getInstance();

        $select = new Query\Select();
        $select->select($self->name . '.*')
            ->from($self->name, $self->name)
            ->setFetchType($self->rowClass);

        return $select;
    }

    /**
     * Create Row instance
     *
     * @param  array $data
     *
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
     * <code>
     *     Table::insert(['login' => 'Man', 'email' => 'man@example.com'])
     * </code>
     *
     * @param  array $data Column-value pairs
     *
     * @return string|null Primary key or null
     * @throws Exception\DbException
     */
    public static function insert(array $data)
    {
        $self = static::getInstance();

        $data = static::filterColumns($data);

        if (!count($data)) {
            throw new DbException(
                "Invalid field names of table `{$self->name}`. Please check use of `insert()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->name);

        $sql = "INSERT INTO $table SET " . implode(',', self::prepareStatement($data));
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
     * <code>
     *     Table::insert(['login' => 'Man', 'email' => 'man@domain.com'], ['id' => 42])
     * </code>
     *
     * @param  array $data  Column-value pairs.
     * @param  array $where An array of SQL WHERE clause(s)
     *
     * @return integer The number of rows updated
     * @throws Exception\DbException
     */
    public static function update(array $data, array $where)
    {
        if (!count($where)) {
            throw new DbException(
                "Method `Table::update()` can't update all records in table,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $self = static::getInstance();

        $data = static::filterColumns($data);

        $where = static::filterColumns($where);

        if (!count($data) || !count($where)) {
            throw new DbException(
                "Invalid field names of table `{$self->name}`. Please check use of `update()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->name);

        $sql = "UPDATE $table"
            . " SET " . implode(',', self::prepareStatement($data))
            . " WHERE " . implode(' AND ', self::prepareStatement($where));

        return DbProxy::query($sql, array_merge(array_values($data), array_values($where)));
    }

    /**
     * Deletes existing rows
     *
     * <code>
     *     Table::delete(['login' => 'Man'])
     * </code>
     *
     * @param  array $where An array of SQL WHERE clause(s)
     *
     * @return integer The number of rows deleted
     * @throws Exception\DbException
     */
    public static function delete(array $where)
    {
        if (!count($where)) {
            throw new DbException(
                "Method `Table::delete()` can't delete all records in table,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $self = static::getInstance();

        $where = static::filterColumns($where);

        if (!count($where)) {
            throw new DbException(
                "Invalid field names of table `{$self->name}`. Please check use of `delete()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->name);

        $sql = "DELETE FROM $table"
            . " WHERE " . implode(' AND ', self::prepareStatement($where));
        return DbProxy::query($sql, array_values($where));
    }

    /**
     * Setup relation "one to one" or "one to many"
     *
     * @param  string $key
     * @param  string $model
     * @param  string $foreign
     *
     * @return void
     */
    public function linkTo($key, $model, $foreign)
    {
        Relations::setRelation($this->model, $key, $model, $foreign);
    }

    /**
     * Setup relation "many to many"
     * [table1-key] [table1_key-table2-table3_key] [table3-key]
     *
     * @param  string $model
     * @param  string $link
     *
     * @return void
     */
    public function linkToMany($model, $link)
    {
        Relations::setRelations($this->model, $model, [$link]);
    }
}
