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
    protected $limit = null;

    /**
     * @var integer the index of the first result to retrieve.
     */
    protected $offset = 0;

    /**
     * Sets the maximum number of results to retrieve/update/delete
     *
     * @param  integer $limit  The maximum number of results to retrieve
     * @param  integer $offset The offset of the query
     *
     * @return $this
     */
    public function limit(int $limit, int $offset = 0)
    {
        $this->setLimit($limit);
        $this->setOffset($offset);
        return $this;
    }

    /**
     * Setup limit for the query
     *
     * @param  integer $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * Setup offset for the query
     *
     * @param  integer $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * Prepare string to apply limit inside SQL query
     *
     * @return string
     */
    protected function prepareLimit() : string
    {
        return $this->limit ? ' LIMIT ' . $this->limit . ' OFFSET ' . $this->offset : '';
    }
}
