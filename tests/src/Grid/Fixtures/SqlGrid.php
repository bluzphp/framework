<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid\Fixtures;

use Bluz\Grid\Grid;
use Bluz\Grid\Source\SqlSource;

/**
 * Test Grid based on SQL
 *
 * @category Application
 * @package  Test
 */
class SqlGrid extends Grid
{
    /**
     * @var string
     */
    protected $uid = 'sql';

    /**
     * Init SqlSource
     * @return self
     */
    public function init()
    {
         // Array
         $adapter = new SqlSource();
         $adapter->setSource('SELECT * FROM test');

         $this->setAdapter($adapter);
         $this->setDefaultLimit(10);
         $this->setAllowOrders(['name', 'id', 'status']);
         $this->setAllowFilters(['status', 'id', 'email']);
         $this->setDefaultOrder('name', Grid::ORDER_DESC);

         return $this;
    }
}
