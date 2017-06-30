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
use Bluz\Db\Query\Delete;
use Bluz\Db\Query\Select;
use Bluz\Db\Query\Update;

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
 *
 * @method   Select|Update|Delete addQueryPart(string $sqlPartName, mixed $sqlPart, $append = 'true')
 * @method   mixed getQueryPart(string $queryPartName)
 * @method   string prepareCondition($args = [])
 */
trait Where
{
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
     * @param  string[] ...$condition optional the query restriction predicates
     *
     * @return $this
     */
    public function where(...$condition)
    {
        $condition = $this->prepareCondition($condition);

        return $this->addQueryPart('where', $condition, false);
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
     * @param  string[] ...$condition Optional the query restriction predicates
     *
     * @return $this
     */
    public function andWhere(...$condition)
    {
        $condition = $this->prepareCondition($condition);

        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeBuilder && $where->getType() == 'AND') {
            $where->add($condition);
        } else {
            $where = new CompositeBuilder([$where, $condition]);
        }
        return $this->addQueryPart('where', $where, false);
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
     * @param  string[] ...$condition Optional the query restriction predicates
     *
     * @return $this
     */
    public function orWhere(...$condition)
    {
        $condition = $this->prepareCondition($condition);

        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeBuilder && $where->getType() == 'OR') {
            $where->add($condition);
        } else {
            $where = new CompositeBuilder([$where, $condition], 'OR');
        }
        return $this->addQueryPart('where', $where, false);
    }
}
