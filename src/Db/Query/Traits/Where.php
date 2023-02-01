<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\CompositeBuilder;

/**
 * Order Trait
 *
 * Required for:
 *  - Select Builder
 *  - Update Builder
 *  - Delete Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 */
trait Where
{
    /**
     * @var string|CompositeBuilder|null
     */
    protected $where;

    /**
     * Set WHERE condition
     *
     * Specifies one or more restrictions to the query result
     * Replaces any previously specified restrictions, if any
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->where('u.id = ?', $id)
     *      ;
     * </code>
     *
     * @param array $conditions optional the query restriction predicates
     *
     * @return $this
     */
    public function where(...$conditions): self
    {
        $this->where = $this->prepareCondition($conditions);
        return $this;
    }

    /**
     * Add WHERE .. AND .. condition
     *
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.username LIKE ?', '%Smith%')
     *         ->andWhere('u.is_active = ?', 1);
     * </code>
     *
     * @param string[] $conditions Optional the query restriction predicates
     *
     * @return $this
     */
    public function andWhere(...$conditions): self
    {
        $condition = $this->prepareCondition($conditions);

        if (
            $this->where instanceof CompositeBuilder
            && $this->where->getType() === 'AND'
        ) {
            $this->where->addPart($condition);
        } else {
            $this->where = new CompositeBuilder([$this->where, $condition]);
        }
        return $this;
    }

    /**
     * Add WHERE .. OR .. condition
     *
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->where('u.id = 1')
     *         ->orWhere('u.id = ?', 2);
     * </code>
     *
     * @param string[] $conditions Optional the query restriction predicates
     *
     * @return $this
     */
    public function orWhere(...$conditions): self
    {
        $condition = $this->prepareCondition($conditions);

        if (
            $this->where instanceof CompositeBuilder
            && $this->where->getType() === 'OR'
        ) {
            $this->where->addPart($condition);
        } else {
            $this->where = new CompositeBuilder([$this->where, $condition], 'OR');
        }
        return $this;
    }

    /**
     * prepareWhere
     *
     * @return string
     */
    protected function prepareWhere(): string
    {
        return $this->where ? ' WHERE ' . $this->where : '';
    }
}
