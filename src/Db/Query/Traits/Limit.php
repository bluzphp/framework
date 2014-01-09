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
 * Limit Trait, required for:
 *  - Select Builder
 *  - Update Builder
 *  - Delete Builder
 *
 * @category Bluz
 * @package  Db
 * @subpackage Query
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:46
 */
trait Limit {

    /**
     * @var integer The maximum number of results to retrieve/update/delete
     */
    protected $limit = null;

    /**
     * @var integer The index of the first result to retrieve.
     */
    protected $offset = 0;

    /**
     * Sets the maximum number of results to retrieve/update/delete
     *
     * @param integer $limit The maximum number of results to retrieve
     * @param integer $offset
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        $this->setLimit($limit);
        $this->setOffset($offset);
        return $this;
    }

    /**
     * Setup limit for the query
     *
     * @param integer $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * Setup offset for the query
     *
     * @param integer $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }
}
