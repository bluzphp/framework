<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Grid;

/**
 * Data
 *
 * @category Bluz
 * @package  Grid
 *
 * @author   AntonShevchuk
 * @created  16.08.12 9:55
 */
class Data extends \ArrayIterator
{
    /**
     * How many data rows w/out limits
     *
     * @var integer
     */
    protected $total;

    /**
     * set total rows
     *
     * @param $total
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = (int)$total;
        return $this;
    }

    /**
     * total rows
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }
}
