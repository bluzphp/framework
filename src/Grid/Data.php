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
namespace Bluz\Grid;

/**
 * Grid Data
 *
 * @package  Bluz\Grid
 * @author   AntonShevchuk
 */
class Data extends \ArrayIterator
{
    /**
     * @var integer how many data rows w/out limits
     */
    protected $total;

    /**
     * Set total rows
     *
     * @param  integer $total
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = (int)$total;
        return $this;
    }

    /**
     * Get total rows
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }
}
