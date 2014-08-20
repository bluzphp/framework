<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid\Fixtures;

use Bluz\Db\Query\Select;
use Bluz\Grid\Grid;
use Bluz\Grid\Source\SelectSource;

/**
 * Test Grid based on SQL
 *
 * @category Application
 * @package  Test
 */
class SelectGrid extends Grid
{
    /**
     * @var string
     */
    protected $uid = 'sql';

    /**
     * init
     * 
     * @return self
     */
    public function init()
    {
         // Array
         $adapter = new SelectSource();

         $select = new Select();
         $select->select('*')->from('test', 't');

         $adapter->setSource($select);

         $this->setAdapter($adapter);
         $this->setDefaultLimit(10);
         $this->setAllowOrders(['name', 'id', 'status']);
         $this->setAllowFilters(['status', 'id', 'email']);

         return $this;
    }
}
