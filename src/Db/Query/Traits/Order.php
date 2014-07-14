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
namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Delete;
use Bluz\Db\Query\Select;
use Bluz\Db\Query\Update;

/**
 * Order Trait, required for:
 *  - Select Builder
 *  - Update Builder
 *  - Delete Builder
 *
 * @package Bluz\Db\Query\Traits
 *
 * @method Select|Update|Delete addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:00
 */
trait Order
{
    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param string $sort expression
     * @param string $order direction
     * @return Select|Update|Delete
     */
    public function orderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        return $this->addQueryPart('orderBy', $sort .' '. ('ASC' == $order ? 'ASC' : 'DESC'), false);
    }

    /**
     * Adds an ordering to the query results
     *
     * @param string $sort expression
     * @param string $order direction
     * @return Select|Update|Delete
     */
    public function addOrderBy($sort, $order = 'ASC')
    {
        $order = strtoupper($order);
        return $this->addQueryPart('orderBy', $sort .' '. ('ASC' == $order ? 'ASC' : 'DESC'), true);
    }
}
