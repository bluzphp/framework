<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query;

use Bluz\Db\Db;
use Bluz\Db\Exception\DbException;
use Bluz\Db\Query\CompositeBuilder;

/**
 * Query Builders classes is responsible to dynamically create SQL queries
 * Based on Doctrine QueryBuilder code
 *
 * @see https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php
 */
abstract class AbstractBuilder
{
    /**
     * @var Db
     */
    protected $adapter = null;

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
    protected $paramTypes = array();

    /**
     * Execute this query using the bound parameters and their types
     *
     * @return mixed
     */
    public function execute()
    {
        return $this->getAdapter()->query($this->getSQL(), $this->params, $this->paramTypes);
    }

    /**
     * Sets a DB adapter.
     *
     * @param Db $adapter DB adapter for table to use
     * @return self instance
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
     * Return the complete SQL string formed by the current specifications
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('User', 'u');
     *     echo $qb->getSQL(); // SELECT u FROM User u
     * </code>
     *
     * @return string The SQL query string.
     */
    abstract public function getSql();

    /**
     * Return the complete SQL string formed for use
     *
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
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id')
     *         ->setParameter(':user_id', 1);
     * </code>
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
        $this->paramTypes[$key] = $type;

        return $this;
    }

    /**
     * Sets a collection of query parameters for the query being constructed
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.id = :user_id1 OR u.id = :user_id2')
     *         ->setParameters(array(
     *             ':user_id1' => 1,
     *             ':user_id2' => 2
     *         ));
     * </code>
     *
     * @param array $params The query parameters to set
     * @param array $types  The query parameters types to set
     * @return self instance
     */
    public function setParameters(array $params, array $types = array())
    {
        $this->paramTypes = $types;
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
     * @param string  $sqlPart
     * @param boolean $append
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
                foreach ($sqlPart as $part) {
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

            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;

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
     * prepareCondition
     *
     * @param array $args
     * @internal param $condition
     * @return string
     */
    protected function prepareCondition($args = array())
    {
        /**
         * <code>
         *   prepareCondition("WHERE id IN (?)", [..,..]);
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
