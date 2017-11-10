<?php
/**
 * @namespace
 */

namespace Bluz\Db;

use Bluz\Db\Exception\InvalidPrimaryKeyException;

/**
 * TableInterface
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 */
interface TableInterface
{
    /**
     * Get Table instance
     *
     * @return TableInterface
     */
    public static function getInstance();

    /**
     * Get primary key(s)
     *
     * @return array
     * @throws InvalidPrimaryKeyException if primary key was not set or has wrong format
     */
    public function getPrimaryKey() : array;

    /**
     * Get table name
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get model name
     *
     * @return string
     */
    public function getModel() : string;

    /**
     * Create Row instance
     *
     * @param  array $data
     *
     * @return RowInterface
     */
    public static function create(array $data = []) : RowInterface;

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
     * @return RowInterface[]
     */
    public static function find(...$keys) : array;

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
     * @return RowInterface[]
     */
    public static function findWhere(...$where) : array;

    /**
     * Find row by primary key
     *
     * @param  mixed $primaryKey
     *
     * @return RowInterface
     */
    public static function findRow($primaryKey) : ?RowInterface;

    /**
     * Find row by where condition
     *
     * @param  array $whereList
     *
     * @return RowInterface
     */
    public static function findRowWhere(array $whereList) : ?RowInterface;

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
    public static function select() : Query\Select;

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
     */
    public static function insert(array $data);

    /**
     * Updates existing rows
     *
     * <code>
     *     Table::update(['login' => 'Man', 'email' => 'man@domain.com'], ['id' => 42])
     * </code>
     *
     * @param  array $data  Column-value pairs.
     * @param  array $where An array of SQL WHERE clause(s)
     *
     * @return integer The number of rows updated
     */
    public static function update(array $data, array $where) : int;

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
     */
    public static function delete(array $where) : int;
}
