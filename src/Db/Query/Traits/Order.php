<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

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
trait Order
{
    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param  string $sort  Sort expression
     * @param  string $order Sort direction (ASC or DESC)
     *
     * @return $this
     */
    public function orderBy(string $sort, string $order = 'ASC')
    {
        $order = 'ASC' === strtoupper($order) ? 'ASC' : 'DESC';
        $this->orderBy = [$sort => $order];
        return $this;
    }

    /**
     * Adds an ordering to the query results
     *
     * @param  string $sort  Sort expression
     * @param  string $order Sort direction (ASC or DESC)
     *
     * @return $this
     */
    public function addOrderBy(string $sort, string $order = 'ASC')
    {
        $order = 'ASC' === strtoupper($order) ? 'ASC' : 'DESC';
        $this->orderBy[$sort] = $order;
        return $this;
    }

    /**
     * Prepare string to apply it inside SQL query
     *
     * @return string
     */
    protected function prepareOrderBy() : string
    {
        if (empty($this->orderBy)) {
            return '';
        }
        $orders = [];
        foreach ($this->orderBy as $column => $order) {
            $orders[] = $column . ' ' . $order;
        }
        return ' ORDER BY ' . implode(', ', $orders);
    }
}
