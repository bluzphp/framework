<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Proxy\Db;

/**
 * From Trait
 *
 * Required for:
 *  - Select Builder
 *  - Delete Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 */
trait From
{
    /**
     * <code>
     * [
     *     'table' => 'users',
     *     'alias' => 'u'
     * ]
     * </code>
     *
     * @var array
     */
    protected $from = [];

    /**
     * <code>
     * [
     *     'u' => [
     *         'joinType' => 'inner',
     *         'joinTable' => $join,
     *         'joinAlias' => $alias,
     *         'joinCondition' => $condition
     * ]
     * </code>
     *
     * @var array[]
     */
    protected $join = [];

    /**
     * Set FROM
     *
     * Create and add a query root corresponding to the table identified by the
     * given alias, forming a cartesian product with any existing query roots
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.id')
     *         ->from('users', 'u')
     * </code>
     *
     * @param  string $from  The table
     * @param  string $alias The alias of the table
     *
     * @return $this
     */
    public function from($from, $alias)
    {
        $this->aliases[] = $alias;

        $this->from[] = [
            'table' => $from,
            'alias' => $alias
        ];

        return $this;
    }

    /**
     * Creates and adds a join to the query
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->join('u', 'phone', 'p', 'p.is_primary = 1');
     * </code>
     *
     * @param  string $fromAlias the alias that points to a from clause
     * @param  string $join      the table name to join
     * @param  string $alias     the alias of the join table
     * @param  string $condition the condition for the join
     *
     * @return $this
     */
    public function join($fromAlias, $join, $alias, $condition = null)
    {
        return $this->innerJoin($fromAlias, $join, $alias, $condition);
    }

    /**
     * Creates and adds a join to the query
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->innerJoin('u', 'phone', 'p', 'p.is_primary = 1');
     * </code>
     *
     * @param  string $fromAlias the alias that points to a from clause
     * @param  string $join      the table name to join
     * @param  string $alias     the alias of the join table
     * @param  string $condition the condition for the join
     *
     * @return $this
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        $this->join[$fromAlias][] = [
            'joinType' => 'inner',
            'joinTable' => $join,
            'joinAlias' => $alias,
            'joinCondition' => $condition
        ];
        return $this;
    }

    /**
     * Creates and adds a left join to the query.
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phone', 'p', 'p.is_primary = 1');
     * </code>
     *
     * @param  string $fromAlias the alias that points to a from clause
     * @param  string $join      the table name to join
     * @param  string $alias     the alias of the join table
     * @param  string $condition the condition for the join
     *
     * @return $this
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        $this->join[$fromAlias][] = [
            'joinType' => 'left',
            'joinTable' => $join,
            'joinAlias' => $alias,
            'joinCondition' => $condition
        ];
        return $this;
    }

    /**
     * Creates and adds a right join to the query.
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->rightJoin('u', 'phone', 'p', 'p.is_primary = 1');
     * </code>
     *
     * @param  string $fromAlias the alias that points to a from clause
     * @param  string $join      the table name to join
     * @param  string $alias     the alias of the join table
     * @param  string $condition the condition for the join
     *
     * @return $this
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        $this->aliases[] = $alias;

        $this->join[$fromAlias][] = [
            'joinType' => 'right',
            'joinTable' => $join,
            'joinAlias' => $alias,
            'joinCondition' => $condition
        ];
        return $this;
    }

    /**
     * setFromQueryPart
     *
     * @param  string $table
     *
     * @return self
     */
    protected function setFromQueryPart($table)
    {
        return $this->from($table, $table);
    }

    /**
     * Prepare From query part
     *
     * @return string
     */
    protected function prepareFrom() : string
    {
        $fromClauses = [];
        // Loop through all FROM clauses
        foreach ($this->from as $from) {
            $fromClause = Db::quoteIdentifier($from['table']) . ' AS ' . $from['alias']
                . $this->getSQLForJoins($from['alias']);

            $fromClauses[$from['alias']] = $fromClause;
        }

        return ' FROM ' . implode(', ', $fromClauses);
    }

    /**
     * Generate SQL string for JOINs
     *
     * @param  string $fromAlias alias of table
     *
     * @return string
     */
    protected function getSQLForJoins($fromAlias) : string
    {
        if (!isset($this->join[$fromAlias])) {
            return '';
        }

        $query = '';

        foreach ($this->join[$fromAlias] as $join) {
            $query .= ' ' . strtoupper($join['joinType'])
                . ' JOIN ' . Db::quoteIdentifier($join['joinTable']) . ' AS ' . $join['joinAlias']
                . ' ON ' . $join['joinCondition'];
            $query .= $this->getSQLForJoins($join['joinAlias']);
        }

        return $query;
    }
}
