<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

use Bluz\Db\Exception\DbException;
use Bluz\Proxy\Db;

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
     * <code>
     * [
     *     'u.id', 'u.name',
     *     'p.id', 'p.title'
     * ]
     * </code>
     *
     * @var array[]
     */
    protected $select = [];
    protected $groupBy = [];
    protected $having = null;

    /**
     * @var mixed PDO fetch types or object class
     */
    protected $fetchType = \PDO::FETCH_ASSOC;

    /**
     * {@inheritdoc}
     *
     * @param  integer|string|object $fetchType
     *
     * @return integer|string|array
     */
    public function execute($fetchType = null)
    {
        if (!$fetchType) {
            $fetchType = $this->fetchType;
        }

        switch ($fetchType) {
            case (!is_int($fetchType)):
                return Db::fetchObjects($this->getSql(), $this->params, $fetchType);
            case \PDO::FETCH_CLASS:
                return Db::fetchObjects($this->getSql(), $this->params);
            case \PDO::FETCH_ASSOC:
            default:
                return Db::fetchAll($this->getSql(), $this->params);
        }
    }

    /**
     * Setup fetch type, any of PDO, or any Class
     *
     * @param  string $fetchType
     *
     * @return Select instance
     */
    public function setFetchType($fetchType): Select
    {
        $this->fetchType = $fetchType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(): string
    {
        return $this->prepareSelect()
            . $this->prepareFrom()
            . $this->prepareWhere()
            . $this->prepareGroupBy()
            . $this->prepareHaving()
            . $this->prepareOrderBy()
            . $this->prepareLimit();
    }

    /**
     * Specifies an item that is to be returned in the query result
     * Replaces any previously specified selections, if any
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.id', 'p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phone', 'p', 'u.id = p.user_id');
     * </code>
     *
     * @param  string[] $select the selection expressions
     *
     * @return Select instance
     */
    public function select(...$select): Select
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.id')
     *         ->addSelect('p.id')
     *         ->from('users', 'u')
     *         ->leftJoin('u', 'phone', 'u.id = p.user_id');
     * </code>
     *
     * @param  string[] $select the selection expression
     *
     * @return Select instance
     */
    public function addSelect(string ...$select): Select
    {
        $this->select = array_merge($this->select, $select);
        return $this;
    }

    /**
     * Get current select query part
     *
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.id');
     * </code>
     *
     * @param  string[] $groupBy the grouping expression
     *
     * @return Select instance
     */
    public function groupBy(string ...$groupBy): Select
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * Adds a grouping expression to the query.
     *
     * Example
     * <code>
     *     $sb = new Select();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->groupBy('u.lastLogin');
     *         ->addGroupBy('u.createdAt')
     * </code>
     *
     * @param  string[] $groupBy the grouping expression
     *
     * @return Select instance
     */
    public function addGroupBy(string ...$groupBy): Select
    {
        $this->groupBy = array_merge($this->groupBy, $groupBy);
        return $this;
    }

    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any
     *
     * @param  string[] $conditions the query restriction predicates
     *
     * @return Select
     */
    public function having(...$conditions): Select
    {
        $this->having = $this->prepareCondition($conditions);
        return $this;
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * conjunction with any existing having restrictions
     *
     * @param  string[] $conditions the query restriction predicates
     *
     * @return Select
     */
    public function andHaving(...$conditions): Select
    {
        $condition = $this->prepareCondition($conditions);

        if ($this->having instanceof CompositeBuilder
            && $this->having->getType() === 'AND') {
            $this->having->addPart($condition);
        } else {
            $this->having = new CompositeBuilder([$this->having, $condition]);
        }
        return $this;
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions
     *
     * @param  string[] $conditions the query restriction predicates
     *
     * @return Select
     */
    public function orHaving(...$conditions): Select
    {
        $condition = $this->prepareCondition($conditions);

        if ($this->having instanceof CompositeBuilder
            && $this->having->getType() === 'OR') {
            $this->having->addPart($condition);
        } else {
            $this->having = new CompositeBuilder([$this->having, $condition], 'OR');
        }
        return $this;
    }

    /**
     * Setup offset like a page number, start from 1
     *
     * @param  integer $page
     *
     * @return Select
     * @throws DbException
     */
    public function setPage(int $page = 1): Select
    {
        if (!$this->limit) {
            throw new DbException('Please setup limit for use method `setPage`');
        }
        $this->offset = $this->limit * ($page - 1);
        return $this;
    }

    /**
     * Prepare Select query part
     *
     * @return string
     */
    protected function prepareSelect(): string
    {
        return 'SELECT ' . implode(', ', $this->select);
    }

    /**
     * prepareWhere
     *
     * @return string
     */
    protected function prepareGroupBy(): string
    {
        return !empty($this->groupBy) ? ' GROUP BY ' . implode(', ', $this->groupBy): '';
    }

    /**
     * prepareWhere
     *
     * @return string
     */
    protected function prepareHaving(): string
    {
        return $this->having ? ' HAVING ' . $this->having : '';
    }
}
