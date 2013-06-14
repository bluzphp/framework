<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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
namespace Bluz\Db\Query;

use Bluz\Db\Db;
use Bluz\Db\DbException;

/**
 * Builder of SELECT queries
 */
class SelectBuilder extends AbstractBuilder
{
    /**
     * @var integer The index of the first result to retrieve.
     */
    protected $offset = 0;

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->db->fetchAll($this->getSQL(), $this->params, $this->paramTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql()
    {
        $query = "SELECT " . implode(', ', $this->sqlParts['select']) . "\n FROM ";

        $fromClauses = array();
        $knownAliases = array();

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            $knownAliases[$from['alias']] = true;
            $fromClause = $from['table'] . ' ' . $from['alias']
                . $this->getSQLForJoins($from['alias'], $knownAliases);

            $fromClauses[$from['alias']] = $fromClause;
        }

        $query .= implode(', ', $fromClauses)
            . ($this->sqlParts['where'] !== null ? "\n WHERE " . ((string) $this->sqlParts['where']) : '')
            . ($this->sqlParts['groupBy'] ? "\n GROUP BY " . implode(', ', $this->sqlParts['groupBy']) : '')
            . ($this->sqlParts['having'] !== null ? "\n HAVING " . ((string) $this->sqlParts['having']) : '')
            . ($this->sqlParts['orderBy'] ? "\n ORDER BY " . implode(', ', $this->sqlParts['orderBy']) : '')
            . ($this->limit ? "\n LIMIT {$this->limit} OFFSET {$this->offset}" : "")
        ;

        return $query;
    }

    /**
     * Specifies an item that is to be returned in the query result
     * Replaces any previously specified selections, if any
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.id', 'p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id');
     * </code>
     *
     * @param mixed $select The selection expressions.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function select($select)
    {
        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, false);
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.id')
     *         ->addSelect('p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phonenumbers', 'u.id = p.user_id');
     * </code>
     *
     * @param mixed $select The selection expression.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function addSelect($select)
    {
        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, true);
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.id');
     * </code>
     *
     * @param mixed $groupBy The grouping expression.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy);
    }

    /**
     * Adds a grouping expression to the query.
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.lastLogin');
     *         ->addGroupBy('u.createdAt')
     * </code>
     *
     * @param mixed $groupBy The grouping expression.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function addGroupBy($groupBy)
    {
        if (empty($groupBy)) {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, true);
    }


    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        $order = ('ASC' == $order ? 'ASC' : 'DESC');
        return $this->add('orderBy', $sort . ' ' . $order);
    }

    /**
     * Adds an ordering to the query results
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return SelectBuilder This QueryBuilder instance.
     */
    public function addOrderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        $order = ('ASC' == $order ? 'ASC' : 'DESC');
        return $this->add('orderBy', $sort . ' ' . $order, true);
    }
    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any.
     *
     * @param mixed $condition The query restriction predicates
     * @return SelectBuilder
     */
    public function having($condition)
    {
        $condition = $this->prepareCondition(func_get_args());
        return $this->add('having', $condition);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * conjunction with any existing having restrictions
     *
     * @param mixed $condition The restriction to append
     * @return SelectBuilder
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

        return $this->add('having', $having);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
     * @param mixed $condition The restriction to add
     * @return SelectBuilder
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

        return $this->add('having', $having);
    }

    /**
     * Setup offset for the query
     *
     * @param $offset
     * @return SelectBuilder
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    /**
     * Setup offset like a page number, start from 1
     *
     * @param int $page
     * @throws DbException
     * @return SelectBuilder
     */
    public function setPage($page = 1)
    {
        if (!$this->limit) {
            throw new DbException("Please setup limit for use method `setPage`");
        }
        $this->offset = $this->limit * ($page - 1);
        return $this;
    }
}