<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

use Bluz\Proxy\Db;

/**
 * Query Builders classes is responsible to dynamically create SQL queries
 * Based on Doctrine QueryBuilder code
 *
 * @package Bluz\Db\Query
 * @link    https://github.com/bluzphp/framework/wiki/Db-Query
 * @link    https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php
 */
abstract class AbstractBuilder
{
    /**
     * @var array list of table aliases
     */
    protected $aliases = [];

    /**
     * @var array the query parameters
     */
    protected $params = [];

    /**
     * @var array the parameter type map of this query
     */
    protected $types = [];

    /**
     * @var string the complete SQL string for this query
     */
    protected $sql;

    /**
     * Execute this query using the bound parameters and their types
     *
     * @return integer|string|array
     */
    public function execute()
    {
        return Db::query($this->getSql(), $this->params, $this->types);
    }

    /**
     * Return the complete SQL string formed by the current specifications
     *
     * Example
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('User', 'u');
     *     echo $qb->getSql(); // SELECT u FROM User u
     * </code>
     *
     * @return string The SQL query string
     */
    abstract public function getSql() : string;

    /**
     * Return the complete SQL string formed for use
     *
     * Example
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('id = ?', 42);
     *     echo $qb->getQuery(); // SELECT u FROM User u WHERE id = "42"
     * </code>
     *
     * @return string
     */
    public function getQuery() : string
    {
        $sql = $this->getSql();

        $sql = str_replace(['%', '?'], ['%%', '"%s"'], $sql);

        // replace mask by data
        return vsprintf($sql, $this->getParams());
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed
     *
     * @param  mixed $key The key (index or name) of the bound parameter
     *
     * @return mixed The value of the bound parameter.
     */
    public function getParam($key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Sets a query parameter for the query being constructed
     *
     * Example
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id')
     *         ->setParameter(':user_id', 1);
     * </code>
     *
     * @param  string|int|null $key   The parameter position or name
     * @param  mixed      $value The parameter value
     * @param  integer    $type  PDO::PARAM_*
     *
     * @return self
     */
    public function setParam($key, $value, $type = \PDO::PARAM_STR)
    {
        if (null === $key) {
            $key = count($this->params);
        }

        $this->params[$key] = $value;
        $this->types[$key] = $type;

        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed
     *
     * @return array The currently defined query parameters
     */
    public function getParams() : array
    {
        return $this->params;
    }

    /**
     * Sets a collection of query parameters for the query being constructed
     *
     * Example
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id1 OR u.id = :user_id2')
     *         ->setParameters([
     *             ':user_id1' => 1,
     *             ':user_id2' => 2
     *         ]);
     * </code>
     *
     * @param  array $params The query parameters to set
     * @param  array $types  The query parameters types to set
     *
     * @return self
     */
    public function setParams(array $params, array $types = [])
    {
        $this->types = $types;
        $this->params = $params;

        return $this;
    }

    /**
     * Prepare condition
     *
     * <code>
     *     $builder->prepareCondition("WHERE id IN (?)", [..,..]);
     * </code>
     *
     * @param  array $args
     *
     * @return string
     */
    protected function prepareCondition(array $args = []) : string
    {
        $condition = array_shift($args);
        foreach ($args as &$value) {
            if (is_array($value)) {
                $replace = implode(',', array_fill(0, count($value), ':REPLACE:'));
                $condition = preg_replace('/\?/', $replace, $condition, 1);
                foreach ($value as $part) {
                    $this->setParam(null, $part);
                }
            } else {
                $this->setParam(null, $value);
            }
        }
        unset($value);

        $condition = preg_replace('/(\:REPLACE\:)/', '?', $condition);

        return $condition;
    }

    /**
     * Gets a string representation of this QueryBuilder which corresponds to
     * the final SQL query being constructed.
     *
     * @return string The string representation of this QueryBuilder.
     */
    public function __toString()
    {
        return $this->getSql();
    }
}
