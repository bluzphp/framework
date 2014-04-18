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

use Bluz\Db\Exception\DbException;

/**
 * Builder of SELECT queries
 *
 * @package Bluz\Db\Query
 */
class Select extends AbstractBuilder
{
    use Traits\From;
    use Traits\Where;
    use Traits\Order;
    use Traits\Limit;

    /**
     * PDO fetch types
     * or object class
     *
     * @var mixed
     */
    protected $fetchType = \PDO::FETCH_ASSOC;

    /**
     * {@inheritdoc}
     *
     * @param null $fetchType
     * @return array|mixed
     */
    public function execute($fetchType = null)
    {
        if (!$fetchType) {
            $fetchType = $this->fetchType;
        }

        switch ($fetchType) {
            case (!is_int($fetchType)):
                return $this->getAdapter()->fetchObjects($this->getSQL(), $this->params, $fetchType);
            case \PDO::FETCH_CLASS:
                return $this->getAdapter()->fetchObjects($this->getSQL(), $this->params);
            case \PDO::FETCH_ASSOC:
            default:
                return $this->getAdapter()->fetchAll($this->getSQL(), $this->params);
        }
    }

    /**
     * Setup fetch type, any of PDO, or any Class
     *
     * @param $fetchType
     * @return Select This QueryBuilder instance.
     */
    public function setFetchType($fetchType)
    {
        $this->fetchType = $fetchType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSql()
    {
        $query = "SELECT " . implode(', ', $this->sqlParts['select']) . " FROM ";

        $fromClauses = array();
        $knownAliases = array();

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            $knownAliases[$from['alias']] = true;
            $fromClause = $from['table'] . ' ' . $from['alias']
                . $this->getSQLForJoins($from['alias'], $knownAliases);

            $fromClauses[$from['alias']] = $fromClause;
        }

        $query .= join(', ', $fromClauses)
            . ($this->sqlParts['where'] !== null ? " WHERE " . ((string) $this->sqlParts['where']) : "")
            . ($this->sqlParts['groupBy'] ? " GROUP BY " . join(", ", $this->sqlParts['groupBy']) : "")
            . ($this->sqlParts['having'] !== null ? " HAVING " . ((string) $this->sqlParts['having']) : "")
            . ($this->sqlParts['orderBy'] ? " ORDER BY " . join(", ", $this->sqlParts['orderBy']) : "")
            . ($this->limit ? " LIMIT ". $this->limit ." OFFSET ". $this->offset : "")
        ;

        return $query;
    }

    /**
     * Specifies an item that is to be returned in the query result
     * Replaces any previously specified selections, if any
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.id', 'p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');
     *
     * @param mixed $select The selection expressions.
     * @return Select This QueryBuilder instance.
     */
    public function select($select)
    {
        $selects = is_array($select) ? $select : func_get_args();

        return $this->addQueryPart('select', $selects, false);
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.id')
     *         ->addSelect('p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phonenumbers', 'u.id = p.user_id');
     *
     * @param mixed $select The selection expression.
     * @return Select This QueryBuilder instance.
     */
    public function addSelect($select)
    {
        $selects = is_array($select) ? $select : func_get_args();

        return $this->addQueryPart('select', $selects, true);
    }

    /**
     * Creates and adds a join to the query
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->join('u', 'phonenumbers', 'p', 'p.is_primary = 1');
     *
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string $condition The condition for the join
     * @return self instance
     */
    public function join($fromAlias, $join, $alias, $condition = null)
    {
        return $this->innerJoin($fromAlias, $join, $alias, $condition);
    }

    /**
     * Creates and adds a join to the query
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->innerJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
     *
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string $condition The condition for the join
     * @return self instance
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        return $this->addQueryPart(
            'join',
            array(
                $fromAlias => array(
                    'joinType'      => 'inner',
                    'joinTable'     => $join,
                    'joinAlias'     => $alias,
                    'joinCondition' => $condition
                )
            ),
            true
        );
    }

    /**
     * Creates and adds a left join to the query.
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
     *
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string $condition The condition for the join
     * @return self instance
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        return $this->addQueryPart(
            'join',
            array(
                $fromAlias => array(
                    'joinType'      => 'left',
                    'joinTable'     => $join,
                    'joinAlias'     => $alias,
                    'joinCondition' => $condition
                )
            ),
            true
        );
    }

    /**
     * Creates and adds a right join to the query.
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->rightJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
     *
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string $condition The condition for the join
     * @return self instance
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        return $this->addQueryPart(
            'join',
            array(
                $fromAlias => array(
                    'joinType'      => 'right',
                    'joinTable'     => $join,
                    'joinAlias'     => $alias,
                    'joinCondition' => $condition
                )
            ),
            true
        );
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.id');
     *
     * @param mixed $groupBy The grouping expression.
     * @return Select This QueryBuilder instance.
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->addQueryPart('groupBy', $groupBy);
    }

    /**
     * Adds a grouping expression to the query.
     *
     * Example
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.lastLogin');
     *         ->addGroupBy('u.createdAt')
     *
     * @param mixed $groupBy The grouping expression.
     * @return Select This QueryBuilder instance.
     */
    public function addGroupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->addQueryPart('groupBy', $groupBy, true);
    }

    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return Select This QueryBuilder instance.
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        $order = ('ASC' == $order ? 'ASC' : 'DESC');
        return $this->addQueryPart('orderBy', $sort . ' ' . $order);
    }

    /**
     * Adds an ordering to the query results
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return Select This QueryBuilder instance.
     */
    public function addOrderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        $order = ('ASC' == $order ? 'ASC' : 'DESC');
        return $this->addQueryPart('orderBy', $sort . ' ' . $order, true);
    }

    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any.
     *
     * @param mixed $condition The query restriction predicates
     * @return Select
     */
    public function having($condition)
    {
        $condition = $this->prepareCondition(func_get_args());
        return $this->addQueryPart('having', $condition);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * conjunction with any existing having restrictions
     *
     * @param mixed $condition The restriction to append
     * @return Select
     */
    public function andHaving($condition)
    {
        $condition = $this->prepareCondition(func_get_args());
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeBuilder) {
            $having->add($condition);
        } else {
            $having = new CompositeBuilder([$having, $condition]);
        }

        return $this->addQueryPart('having', $having);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
     * @param mixed $condition The restriction to add
     * @return Select
     */
    public function orHaving($condition)
    {
        $condition = $this->prepareCondition(func_get_args());
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeBuilder) {
            $having->add($condition);
        } else {
            $having = new CompositeBuilder([$having, $condition], 'OR');
        }

        return $this->addQueryPart('having', $having);
    }

    /**
     * Setup offset like a page number, start from 1
     *
     * @param int $page
     * @throws DbException
     * @return Select
     */
    public function setPage($page = 1)
    {
        if (!$this->limit) {
            throw new DbException("Please setup limit for use method `setPage`");
        }
        $this->offset = $this->limit * ($page - 1);
        return $this;
    }

    /**
     * Generate SQL string for JOINs
     *
     * @param $fromAlias
     * @return string
     */
    protected function getSQLForJoins($fromAlias)
    {
        $sql = '';

        if (isset($this->sqlParts['join'][$fromAlias])) {
            foreach ($this->sqlParts['join'][$fromAlias] as $join) {
                $sql .= ' ' . strtoupper($join['joinType'])
                    . " JOIN " . $join['joinTable'] . ' ' . $join['joinAlias']
                    . " ON " . ((string) $join['joinCondition']);
                $sql .= $this->getSQLForJoins($join['joinAlias']);
            }
        }

        return $sql;
    }
}
