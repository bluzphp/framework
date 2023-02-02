<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Delete;
use Bluz\Db\Query\Select;
use Bluz\Db\Query\Update;

/**
 * Limit Trait
 *
 * Required for:
 *  - Select Builder
 *  - Update Builder
 *  - Delete Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 */
trait Limit
{
    /**
     * @var integer the maximum number of results to retrieve/update/delete
     */
    protected int $limit = 0;

    /**
     * @var integer the index of the first result to retrieve.
     */
    protected int $offset = 0;

    /**
     * Sets the maximum number of results to retrieve/update/delete
     *
     * @param int $limit The maximum number of results to retrieve
     * @param int $offset The offset of the query
     * @return Delete|Select|Update
     */
    public function limit(int $limit, int $offset = 0): Delete|Update|Select
    {
        $this->setLimit($limit);
        $this->setOffset($offset);
        return $this;
    }

    /**
     * Setup limit for the query
     *
     * @param int $limit
     * @return Delete|Select|Update
     */
    public function setLimit(int $limit): Delete|Update|Select
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Setup offset for the query
     *
     * @param int $offset
     *
     * @return Delete|Select|Update
     */
    public function setOffset(int $offset): Delete|Update|Select
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Prepare string to apply limit inside SQL query
     *
     * @return string
     */
    protected function prepareLimit(): string
    {
        return $this->limit ? ' LIMIT ' . $this->limit . ' OFFSET ' . $this->offset : '';
    }
}
