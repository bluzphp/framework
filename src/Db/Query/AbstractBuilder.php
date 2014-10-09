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
namespace Bluz\Db\Query;

use Bluz\Proxy\Db;

/**
 * Query Builders classes is responsible to dynamically create SQL queries
 * Based on Doctrine QueryBuilder code
 *
 * @package Bluz\Db\Query
 *
 * @link https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php
 */
abstract class AbstractBuilder
{
    /**
     * Known table aliases
     * @var array
     */
    protected $aliases = array();

    /**
     * @var string The complete SQL string for this query
     */
    protected $sql;

    /**
     * @var array The array of SQL parts collected
     */
    protected $sqlParts = array(
        'select'  => array(),
        'from'    => array(),
        'join'    => array(),
        'set'     => array(),
        'where'   => null,
        'groupBy' => array(),
        'having'  => null,
        'orderBy' => array()
    );

    /**
     * @var array The query parameters
     */
    protected $params = array();

    /**
     * @var array The parameter type map of this query
     */
    protected $types = array();

    /**
     * Execute this query using the bound parameters and their types
     *
     * @return mixed
     */
    public function execute()
    {
        return Db::query($this->getSQL(), $this->params, $this->types);
    }
    
    /**
     * Return the complete SQL string formed by the current specifications
     *
     * Example
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('User', 'u');
     *     echo $qb->getSQL(); // SELECT u FROM User u
     *
     * @return string The SQL query string.
     */
    abstract public function getSql();

    /**
     * Return the complete SQL string formed for use
     *
     * Example
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('User', 'u')
     *         ->where('id = ?', 42);
     *     echo $qb->getQuery(); // SELECT u FROM User u WHERE id = "42"
     *
     * @return string
     */
    public function getQuery()
    {
        $sql = $this->getSql();

        $sql = str_replace('%', '%%', $sql);
        $sql = str_replace('?', '"%s"', $sql);

        // replace mask by data
        return vsprintf($sql, $this->getParameters());
    }

    /**
     * Sets a query parameter for the query being constructed
     *
     * Example
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id')
     *         ->setParameter(':user_id', 1);
     *
     * @param string|integer $key The parameter position or name
     * @param mixed $value The parameter value
     * @param integer $type PDO::PARAM_*
     * @return self instance
     */
    public function setParameter($key, $value, $type = \PDO::PARAM_STR)
    {
        if (null == $key) {
            $key = sizeof($this->params);
        }

        $this->params[$key] = $value;
        $this->types[$key] = $type;

        return $this;
    }

    /**
     * Sets a collection of query parameters for the query being constructed
     *
     * Example
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id1 OR u.id = :user_id2')
     *         ->setParameters(array(
     *             ':user_id1' => 1,
     *             ':user_id2' => 2
     *         ));
     *
     * @param array $params The query parameters to set
     * @param array $types  The query parameters types to set
     * @return self instance
     */
    public function setParameters(array $params, array $types = array())
    {
        $this->types = $types;
        $this->params = $params;

        return $this;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed
     *
     * @param mixed $key The key (index or name) of the bound parameter
     * @return mixed The value of the bound parameter.
     */
    public function getParameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Gets all defined query parameters for the query being constructed
     *
     * @return array The currently defined query parameters
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Either appends to or replaces a single, generic query part
     *
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having' and 'orderBy'
     *
     * @param string  $sqlPartName
     * @param string|array  $sqlPart
     * @param bool $append
     * @return self instance
     */
    protected function addQueryPart($sqlPartName, $sqlPart, $append = false)
    {
        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = array($sqlPart);
        }

        if ($append) {
            if ($sqlPartName == "orderBy" || $sqlPartName == "groupBy"
                || $sqlPartName == "select" || $sqlPartName == "set") {
                foreach ((array)$sqlPart as $part) {
                    $this->sqlParts[$sqlPartName][] = $part;
                }
            } elseif ($isArray && is_array($sqlPart[key($sqlPart)])) {
                $key = key($sqlPart);
                $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            } else {
                $this->sqlParts[$sqlPartName] = $sqlPart;
            }
        } else {
            $this->sqlParts[$sqlPartName] = $sqlPart;
        }
        return $this;
    }

    /**
     * Get a query part by its name
     *
     * @param string $queryPartName
     * @return mixed $queryPart
     */
    public function getQueryPart($queryPartName)
    {
        return $this->sqlParts[$queryPartName];
    }

    /**
     * Reset single SQL part
     *
     * @param string $queryPartName
     * @return self instance
     */
    protected function resetQueryPart($queryPartName)
    {
        $this->sqlParts[$queryPartName] = is_array($this->sqlParts[$queryPartName])
            ? array() : null;

        return $this;
    }

    /**
     * setFromQueryPart
     *
     * @param string $table
     * @return self instance
     */
    protected function setFromQueryPart($table)
    {
        $table = Db::quoteIdentifier($table);
        return $this->addQueryPart('from', array('table' => $table), false);
    }

    /**
     * Prepare condition
     *
     * @param array $args
     * @return string
     */
    protected function prepareCondition($args = array())
    {
        /**
         * <code>
         *     prepareCondition("WHERE id IN (?)", [..,..]);
         * </code>
         */
        $condition = array_shift($args);
        foreach ($args as &$value) {
            if (is_array($value)) {
                $replace = join(',', array_fill(0, sizeof($value), ':REPLACE:'));
                $condition = preg_replace('/\?/', $replace, $condition, 1);
                foreach ($value as $part) {
                    $this->setParameter(null, $part);
                }
            } else {
                $this->setParameter(null, $value);
            }
        }

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
        return $this->getSQL();
    }
}
