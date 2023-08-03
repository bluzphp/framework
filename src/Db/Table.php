<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

use Bluz\Common\Exception\InitializationException;
use Bluz\Common\Instance;
use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Traits\TableRelations;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Db as DbProxy;
use InvalidArgumentException;

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
    protected string $name;

    /**
     * @var string the model name
     */
    protected string $model;

    /**
     * @var array table meta
     */
    protected array $meta = [];

    /**
     * @var string default SQL query for select
     */
    protected string $select = '';

    /**
     * @var array the primary key column or columns (only as array).
     */
    protected array $primary;

    /**
     * @var string the sequence name, required for PostgreSQL
     */
    protected string $sequence;

    /**
     * @var string row class name
     */
    protected string $rowClass;

    /**
     * Create and initialize Table instance
     */
    public function __construct()
    {
        $tableClass = static::class;
        $namespace = class_namespace($tableClass);

        // check table name
        if (!$this->name) {
            throw new InitializationException('The table name should be set before initialization');
        }

        // check primary key(s)
        if (!$this->primary) {
            throw new InitializationException('The table primary key(s) should be set before initialization');
        }
        if (!is_array($this->primary)) {
            throw new InvalidPrimaryKeyException('The primary key must be set as an array');
        }


        // autodetect row class
        if (!$this->rowClass) {
            $this->rowClass = $namespace . '\\Row';
        }

        // setup default select query
        if (empty($this->select)) {
            $this->select = 'SELECT ' . DbProxy::quoteIdentifier($this->name) . '.* ' .
                'FROM ' . DbProxy::quoteIdentifier($this->name);
        }

        Relations::addClassMap($this->model, $tableClass);

        $this->init();
    }

    /**
     * Initialization hook.
     * Subclasses may override this method
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * Get primary key(s)
     *
     * @return array
     */
    public function getPrimaryKey(): array
    {
        return $this->primary;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get model name
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Return information about table columns
     *
     * @return array
     */
    public static function getMeta(): array
    {
        $self = static::getInstance();
        if (empty($self->meta)) {
            $cacheKey = "db.table.{$self->name}";
            $meta = Cache::get($cacheKey);
            if (!$meta) {
                $schema = DbProxy::getOption('connect', 'name');

                $meta = DbProxy::fetchUniqueGroup(
                    '
                    SELECT 
                      COLUMN_NAME AS `name`,
                      DATA_TYPE AS `type`,
                      COLUMN_DEFAULT AS `default`,
                      COLUMN_KEY AS `key`
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
    public static function getColumns(): array
    {
        $self = static::getInstance();
        return array_keys($self::getMeta());
    }

    /**
     * Filter columns for insert/update queries by table columns definition
     *
     * @param array $data
     *
     * @return array
     */
    public function filterColumns(array $data): array
    {
        return array_intersect_key($data, array_flip($this->getColumns()));
    }

    /**
     * Fetching rows by SQL query
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Params for query placeholders
     *
     * @return RowInterface[] of rows results in FETCH_CLASS mode
     */
    protected function fetch(string $sql, array $params = []): array
    {
        return DbProxy::fetchObjects($sql, $params, $this->rowClass);
    }

    /**
     * @inheritDoc
     *
     * @throws DbException
     * @throws InvalidPrimaryKeyException if wrong count of values passed
     * @throws InvalidArgumentException
     */
    public function find(...$keys): array
    {
        $keyNames = array_values($this->getPrimaryKey());
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
        return $this->findWhere(...$whereList);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     * @throws Exception\DbException
     */
    public function findWhere(...$where): array
    {
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
                            ['DbProxy', 'quote'],
                            $keyValue
                        );
                        $keyValue = implode(',', $keyValue);
                        $whereAndTerms[] = $this->name . '.' . $keyName . ' IN (' . $keyValue . ')';
                    } elseif (null === $keyValue) {
                        $whereAndTerms[] = $this->name . '.' . $keyName . ' IS NULL';
                    } elseif (
                        is_string($keyValue)
                        && (str_starts_with($keyValue, '%') || str_ends_with($keyValue, '%'))
                    ) {
                        $whereAndTerms[] = $this->name . '.' . $keyName . ' LIKE ?';
                        $whereParams[] = $keyValue;
                    } else {
                        $whereAndTerms[] = $this->name . '.' . $keyName . ' = ?';
                        $whereParams[] = $keyValue;
                    }
                    if (!is_scalar($keyValue) && !is_null($keyValue)) {
                        throw new InvalidArgumentException(
                            "Wrong arguments of method 'findWhere'.\n" .
                            'Please use syntax described at https://github.com/bluzphp/framework/wiki/Db-Table'
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

        return self::fetch($this->select . ' WHERE ' . $whereClause, $whereParams);
    }

    /**
     * @inheritDoc
     *
     * @throws DbException
     * @throws InvalidArgumentException
     * @throws InvalidPrimaryKeyException
     */
    public function findRow(mixed $primaryKey): ?RowInterface
    {
        $result = $this->find($primaryKey);
        return current($result) ?: null;
    }

    /**
     * @inheritDoc
     *
     * @throws DbException
     * @throws InvalidArgumentException
     */
    public function findRowWhere(array $whereList): ?RowInterface
    {
        $result = $this->findWhere($whereList);
        return current($result) ?: null;
    }

    /**
     * Prepare an array for WHERE or SET statements
     *
     * @param array $where
     *
     * @return array
     */
    private static function prepareStatement(array $where): array
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
    public function select(): Query\Select
    {
        $select = new Query\Select();
        $select->select(DbProxy::quoteIdentifier($this->name) . '.*')
            ->from($this->name, $this->name)
            ->setFetchType($this->rowClass);

        return $select;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception\DbException
     */
    public function insert(array $data): ?string
    {
        $data = $this->filterColumns($data);

        if (!count($data)) {
            throw new DbException(
                "Invalid field names of table `{$this->name}`. Please check use of `insert()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($this->name);

        $sql = "INSERT INTO $table SET " . implode(',', $this->prepareStatement($data));
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
        return DbProxy::handler()->lastInsertId($this->sequence);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception\DbException
     */
    public function update(array $data, array $where): int
    {
        $data = $this->filterColumns($data);

        if (!count($data)) {
            throw new DbException(
                "Invalid field names of table `{$this->name}`. Please check use of `update()` method"
            );
        }

        $where = $this->filterColumns($where);

        if (!count($where)) {
            throw new DbException(
                "Method `Table::update()` can't update all records in the table `{$this->name}`,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }

        $table = DbProxy::quoteIdentifier($this->name);

        $sql = "UPDATE $table SET " . implode(',', self::prepareStatement($data))
            . " WHERE " . implode(' AND ', self::prepareStatement($where));

        return DbProxy::query($sql, array_merge(array_values($data), array_values($where)));
    }

    /**
     * @inheritDoc
     *
     * @throws DbException
     */
    public function delete(array $where): int
    {
        $self = static::getInstance();

        if (!count($where)) {
            throw new DbException(
                "Method `Table::delete()` can't delete all records in the table `{$self->name}`,\n" .
                "please use `Db::query()` instead (of cause if you know what are you doing)"
            );
        }


        $where = $this->filterColumns($where);

        if (!count($where)) {
            throw new DbException(
                "Invalid field names of table `{$this->name}`. Please check use of `delete()` method"
            );
        }

        $table = DbProxy::quoteIdentifier($this->name);

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', self::prepareStatement($where));
        return DbProxy::query($sql, array_values($where));
    }
}
