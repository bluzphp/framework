<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Select;
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
    protected array $from = [];

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
    protected array $join = [];

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
     * @param string $from The table
     * @param string $alias The alias of the table
     * @return Select
     */
    public function from(string $from, string $alias): Select
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
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string|null $condition The condition for the join
     * @return Select
     */
    public function join(string $fromAlias, string $join, string $alias, string $condition = null): Select
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
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string|null $condition The condition for the join
     * @return Select
     */
    public function innerJoin(string $fromAlias, string $join, string $alias, string $condition = null): Select
    {
        return $this->addJoin('inner', $fromAlias, $join, $alias, $condition);
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
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string|null $condition The condition for the join
     * @return Select
     */
    public function leftJoin(string $fromAlias, string $join, string $alias, string $condition = null): Select
    {
        return $this->addJoin('left', $fromAlias, $join, $alias, $condition);
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
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string|null $condition The condition for the join
     * @return Select
     */
    public function rightJoin(string $fromAlias, string $join, string $alias, string $condition = null): Select
    {
        return $this->addJoin('right', $fromAlias, $join, $alias, $condition);
    }

    /**
     * addJoin()
     *
     * @param string $type The type of join
     * @param string $fromAlias The alias that points to a from clause
     * @param string $join The table name to join
     * @param string $alias The alias of the join table
     * @param string|null $condition The condition for the join
     * @return Select
     */
    protected function addJoin(
        string $type,
        string $fromAlias,
        string $join,
        string $alias,
        string $condition = null
    ): Select {
        $this->aliases[] = $alias;

        $this->join[$fromAlias][] = [
            'joinType' => $type,
            'joinTable' => $join,
            'joinAlias' => $alias,
            'joinCondition' => $condition
        ];
        return $this;
    }

    /**
     * setFromQueryPart
     *
     * @param string $table
     * @return Select
     */
    protected function setFromQueryPart(string $table): Select
    {
        return $this->from($table, $table);
    }

    /**
     * Prepare From query part
     *
     * @return string
     */
    protected function prepareFrom(): string
    {
        $fromClauses = [];
        // Loop through all FROM clauses
        foreach ($this->from as $from) {
            $fromClause = Db::quoteIdentifier($from['table']) . ' AS ' . Db::quoteIdentifier($from['alias'])
                . $this->prepareJoins($from['alias']);

            $fromClauses[$from['alias']] = $fromClause;
        }

        return ' FROM ' . implode(', ', $fromClauses);
    }

    /**
     * Generate SQL string for JOINs
     *
     * @param string $fromAlias The alias of the table
     *
     * @return string
     */
    protected function prepareJoins(string $fromAlias): string
    {
        if (!isset($this->join[$fromAlias])) {
            return '';
        }

        $query = '';

        foreach ($this->join[$fromAlias] as $join) {
            $query .= ' ' . strtoupper($join['joinType'])
                . ' JOIN ' . Db::quoteIdentifier($join['joinTable']) . ' AS ' . Db::quoteIdentifier($join['joinAlias'])
                . ' ON ' . $join['joinCondition'];
            $query .= $this->prepareJoins($join['joinAlias']);
        }

        return $query;
    }
}
