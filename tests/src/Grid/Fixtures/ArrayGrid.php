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
use Bluz\Grid\Source\ArraySource;

/**
 * Test Grid based on Array
 *
 * @category Application
 * @package  Test
 */
class ArrayGrid extends Grid
{
    /**
     * @var string
     */
    protected $uid = 'arr';

    /**
     * Init ArraySource
     * @return self
     */
    public function init()
    {
        // Array
        $adapter = new ArraySource();
        $adapter->setSource(
            [
                ['id'=>1, 'name'=>'Foo', 'email'=>'a@bc.com', 'status'=>'active'],
                ['id'=>2, 'name'=>'Bar', 'email'=>'d@ef.com', 'status'=>'active'],
                ['id'=>3, 'name'=>'Foo 2', 'email'=>'m@ef.com', 'status'=>'disable'],
                ['id'=>4, 'name'=>'Foo 3', 'email'=>'j@ef.com', 'status'=>'disable'],
                ['id'=>5, 'name'=>'Foo 4', 'email'=>'g@ef.com', 'status'=>'disable'],
                ['id'=>6, 'name'=>'Foo 5', 'email'=>'r@ef.com', 'status'=>'disable'],
                ['id'=>7, 'name'=>'Foo 6', 'email'=>'m@ef.com', 'status'=>'disable'],
                ['id'=>8, 'name'=>'Foo 7', 'email'=>'n@ef.com', 'status'=>'disable'],
                ['id'=>9, 'name'=>'Foo 8', 'email'=>'w@ef.com', 'status'=>'disable'],
                ['id'=>10, 'name'=>'Foo 9', 'email'=>'l@ef.com', 'status'=>'disable'],
            ]
        );

        $this->setAdapter($adapter);
        $this->setDefaultLimit(4);
        $this->setAllowOrders(['name', 'email', 'id']);
        $this->setAllowFilters(['name', 'email', 'status', 'id']);

        return $this;
    }
}
