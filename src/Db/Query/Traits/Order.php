<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query\Traits;

/**
 * Order Trait, required for:
 *  - Select Builder
 *  - Update Builder
 *  - Delete Builder
 *
 * @category Bluz
 * @package  Db
 * @subpackage Query
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:00
 */
trait Order {

    /**
     * Specifies an ordering for the query results
     * Replaces any previously specified orderings, if any
     *
     * @param string $sort expression
     * @param string $order direction
     * @return $this
     */
    public function orderBy($sort, $order = 'ASC')
    {
        return $this->addQueryPart('orderBy', $sort . ' ' . (! $order ? 'ASC' : $order), false);
    }

    /**
     * Adds an ordering to the query results
     *
     * @param string $sort expression
     * @param string $order direction
     * @return $this
     */
    public function addOrderBy($sort, $order = 'ASC')
    {
        return $this->addQueryPart('orderBy', $sort . ' ' . (! $order ? 'ASC' : $order), true);
    }
}
