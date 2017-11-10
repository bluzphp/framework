<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

use Bluz\Common\Instance;
use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Traits\TableRelations;
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
abstract class Table implements TableInterface
{
    use Instance;
    use TableRelations;

    /**
     * @var string the table name
     */
    protected $name;

    /**
     * @var string the model name
     */
    protected $model;

    /**
     * @var array table meta
     */
    protected $meta = [];

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
     * Get primary key(s)
     *
     * @return array
     * @throws InvalidPrimaryKeyException if primary key was not set or has wrong format
     */
    public function getPrimaryKey() : array
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
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get model name
     *
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }

    /**
     * Return information about table columns
     *
     * @return array
     */
    public static function getMeta()
    {
        $self = static::getInstance();
        if (empty($self->meta)) {
            $cacheKey = "db.table.{$self->name}";
            $meta = Cache::get($cacheKey);
            if (!$meta) {
                $schema = DbProxy::getOption('connect', 'name');

                $meta = DbProxy::fetchUniqueGroup(
                    '
                    SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_KEY
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = ?
                      AND TABLE_NAME = ?',
                    [$schema, $self->getName()]
                );
                Cache::set($cacheKey, $meta, Cache::TTL_NO_EXPIRY, ['system', 'db']);
            }
            $self->meta = $meta;
        }
        return $self->meta;
    }

    /**
     * Return names of table columns
     *
     * @return array
     */
    public static function getColumns()
    {
        $self = static::getInstance();
        return array_keys($self::getMeta());
    }

    /**
     * Filter columns for insert/update queries by table columns definition
     *
     * @param  array $data
     *
     * @return array
     */
    protected static function filterColumns($data) : array
    {
        return array_intersect_key($data, array_flip(static::getColumns()));
    }

    /**
     * Fetching rows by SQL query
     *
     * @param  string $sql    SQL query with placeholders
     * @param  array  $params Params for query placeholders
     *
     * @return RowInterface[] of rows results in FETCH_CLASS mode
     */
    protected static function fetch($sql, $params = []) : array
    {
        $self = static::getInstance();
        return DbProxy::fetchObjects($sql, $params, $self->rowClass);
    }

    /**
     * {@inheritdoc}
     *
     * @throws DbException
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     * @throws \InvalidArgumentException
     */
    public static function find(...$keys) : array
    {
        $keyNames = array_values(static::getInstance()->getPrimaryKey());
        $whereList = [];
        foreach ($keys as $keyValues) {
            $keyValues = (array)$keyValues;
            if (count($keyValues) !== count($keyNames)) {
                throw new InvalidPrimaryKeyException(
                    "Invalid columns for the primary key.\n" .
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
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws Exception\DbException
     */
    public static function findWhere(...$where) : array
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
                "Method `Table::findWhere()` can't return all records from table"
            );
        }

        return self::fetch($self->select . ' WHERE ' . $whereClause, $whereParams);
    }

    /**
     * {@inheritdoc}
     *
     * @throws DbException
     * @throws \InvalidArgumentException
     * @throws InvalidPrimaryKeyException
     */
    public static function findRow($primaryKey) : ?RowInterface
    {
        $result = static::find($primaryKey);
        return current($result) ?: null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DbException
     * @throws \InvalidArgumentException
     */
    public static function findRowWhere(array $whereList) : ?RowInterface
    {
        $result = static::findWhere($whereList);
        return current($result) ?: null;
    }

    /**
     * Prepare array for WHERE or SET statements
     *
     * @param  array $where
     *
     * @return array
     * @throws \Bluz\Common\Exception\ConfigurationException
     */
    private static function prepareStatement(array $where) : array
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
    public static function select() : Query\Select
    {
        $self = static::getInstance();

        $select = new Query\Select();
        $select->select($self->name . '.*')
            ->from($self->name, $self->name)
            ->setFetchType($self->rowClass);

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(array $data = []) : RowInterface
    {
        $rowClass = static::getInstance()->rowClass;
        /** @var Row $row */
        $row = new $rowClass($data);
        $row->setTable(static::getInstance());
        return $row;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Bluz\Common\Exception\ConfigurationException
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
     * {@inheritdoc}
     *
     * @throws \Bluz\Common\Exception\ConfigurationException
     * @throws Exception\DbException
     */
    public static function update(array $data, array $where) : int
    {
        $self = static::getInstance();

        $data = static::filterColumns($data);

        if (!count($data)) {
            throw new DbException(
                "Invalid field names of table `{$self->name}`. Please check use of `update()` method"
            );
        }

        $where = static::filterColumns($where);

        if (!count($where)) {
            throw new DbException(
                "Method `Table::update()` can't update all records in the table `{$self->name}`,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $table = DbProxy::quoteIdentifier($self->name);

        $sql = "UPDATE $table SET " . implode(',', self::prepareStatement($data))
            . " WHERE " . implode(' AND ', self::prepareStatement($where));

        return DbProxy::query($sql, array_merge(array_values($data), array_values($where)));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Bluz\Common\Exception\ConfigurationException
     * @throws \Bluz\Db\Exception\DbException
     */
    public static function delete(array $where) : int
    {
        $self = static::getInstance();

        if (!count($where)) {
            throw new DbException(
                "Method `Table::delete()` can't delete all records in the table `{$self->name}`,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }


        $where = static::filterColumns($where);

        if (!count($where)) {
            throw new DbException(
                "Invalid field names of table `{$self->name}`. Please check use of `delete()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($self->name);

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', self::prepareStatement($where));
        return DbProxy::query($sql, array_values($where));
    }
}
