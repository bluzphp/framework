<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

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
 * @method   Select|Update|Delete addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 */
trait Order
{
    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param  string $sort  Sort expression
     * @param  string $order Sort direction (ASC or DESC)
     * @return $this
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        return $this->addQueryPart('orderBy', $sort .' '. ('ASC' == $order ? 'ASC' : 'DESC'), false);
    }

    /**
     * Adds an ordering to the query results
     *
     * @param  string $sort  Sort expression
     * @param  string $order Sort direction (ASC or DESC)
     * @return $this
     */
    public function addOrderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        return $this->addQueryPart('orderBy', $sort .' '. ('ASC' == $order ? 'ASC' : 'DESC'), true);
    }
}
