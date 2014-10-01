<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid;

use Bluz\Grid\Grid;
use Bluz\Proxy\Request;
use Bluz\Tests\TestCase;
use Bluz\Tests\Grid\Fixtures\ArrayGrid;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class GridTest extends TestCase
{
    /**
     * Reset application
     */
    public function tearDown()
    {
        self::resetApp();
    }

    /**
     * Process Request
     */
    public function testProcessRequest()
    {
        Request::setParam('arr-page', 2);  // 2 page
        Request::setParam('arr-limit', 2); // 2 rows per page
        Request::setParam('arr-order-id', 'desc');
        Request::setParam('arr-filter-name', 'ne-Smith');
        Request::setParam('arr-filter-status', 'disable');

        $grid = new ArrayGrid();

        $this->assertEquals(8, $grid->total());
        $this->assertEquals(4, $grid->pages());
    }

    /**
     * Custom Module and Controller
     */
    public function testCustomControllerAndModule()
    {
        $grid = new ArrayGrid();
        $grid->setModule('module');
        $grid->setController('controller');
        $this->assertEquals('/module/controller', $grid->reset());
    }

    /**
     * @covers \Bluz\Grid\Grid::addOrder
     * @covers \Bluz\Grid\Grid::addOrders
     * @covers \Bluz\Grid\Grid::setOrders
     * @covers \Bluz\Grid\Grid::getOrders
     */
    public function testOrders()
    {
        $orders = [
            'name' => Grid::ORDER_ASC,
            'email' => Grid::ORDER_DESC
        ];

        $grid = new ArrayGrid();
        $grid->setOrders($orders);

        $this->assertEqualsArray($orders, $grid->getOrders());
    }

    /**
     * @covers \Bluz\Grid\Grid::getPrefix
     */
    public function testGetPrefix()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('arr-', $grid->getPrefix());
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongPageThrowException()
    {
        $grid = new ArrayGrid();
        $grid->setPage(0);
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongLimitThrowException()
    {
        $grid = new ArrayGrid();
        $grid->setLimit(0);
    }

    /**
     * @covers \Bluz\Grid\Grid::getDefaultLimit
     */
    public function testGetDefaultLimit()
    {
        $grid = new ArrayGrid();
        $this->assertEquals(4, $grid->getDefaultLimit());
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongDefaultLimitThrowException()
    {
        $grid = new ArrayGrid();
        $grid->setDefaultLimit(0);
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongColumnFilterThrowException()
    {
        $grid = new ArrayGrid();
        $grid->addFilter('not exist', Grid::FILTER_EQ, 'not found');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongFilterNameThrowException()
    {
        $grid = new ArrayGrid();
        $grid->addFilter('id', 'not exist', 'not found');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongOrderThrowException()
    {
        $grid = new ArrayGrid();
        $grid->setDefaultOrder('not exist');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongOrderDirectionThrowException()
    {
        $grid = new ArrayGrid();
        $grid->setDefaultOrder('id', 'not exist');
    }

    /**
     * Helper Filter
     */
    public function testHelperFilter()
    {
        $grid = new ArrayGrid();
        $grid->addFilter('name', Grid::FILTER_NE, 'Smith');
        $this->assertEquals(
            '/index/index/arr-filter-id/ne-1',
            $grid->filter('id', Grid::FILTER_NE, 1)
        );
        $this->assertEquals(
            '/index/index/arr-filter-name/ne-Smith/arr-filter-id/ne-1',
            $grid->filter('id', Grid::FILTER_NE, 1, false)
        );
    }

    /**
     * Helper Filter
     */
    public function testHelperWrongFilterColumnReturnNull()
    {
        $grid = new ArrayGrid();
        $this->assertNull($grid->filter('not exist', Grid::FILTER_NE, 1));
    }

    /**
     * Helper Filter
     */
    public function testHelperWrongFilterNameReturnNull()
    {
        $grid = new ArrayGrid();
        $this->assertNull($grid->filter('id', 'not exist', 1));
    }

    /**
     * Helper First
     */
    public function testHelperFirst()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/', $grid->first());
    }

    /**
     * Helper Last
     */
    public function testHelperLast()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/index/index/arr-page/3', $grid->last());
    }

    /**
     * Helper Limit
     */
    public function testHelperLimit()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/index/index/arr-limit/25', $grid->limit());
    }

    /**
     * Helper Next
     */
    public function testHelperNext()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/index/index/arr-page/2', $grid->next());
    }

    /**
     * Helper Next
     */
    public function testHelperNextReturnNull()
    {
        $grid = new ArrayGrid();
        $grid->setPage(3);
        $this->assertNull($grid->next());
    }

    /**
     * Helper Order
     */
    public function testHelperOrder()
    {
        $grid = new ArrayGrid();
        $this->assertNull($grid->order('not exists'));
        $this->assertEquals('/index/index/arr-order-name/asc', $grid->order('name'));
        $this->assertEquals('/index/index/arr-order-name/asc', $grid->order('name', 'asc', 'asc', false));
    }

    /**
     * Helper Order
     */
    public function testHelperOrderReverseCurrentOrder()
    {
        $grid = new ArrayGrid();
        $grid->setOrder('name');
        $this->assertEquals('/index/index/arr-order-name/desc', $grid->order('name'));
    }

    /**
     * Helper Page
     */
    public function testHelperPage()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/index/index/arr-page/2', $grid->page(2));
        $this->assertNull($grid->page(42));
    }

    /**
     * Helper Pages
     */
    public function testHelperPages()
    {
        $grid = new ArrayGrid();
        $this->assertEquals(3, $grid->pages());
    }

    /**
     * Helper Prev
     */
    public function testHelperPrev()
    {
        $grid = new ArrayGrid();
        $grid->setPage(3);
        $this->assertEquals('/index/index/arr-page/2', $grid->prev());
    }

    /**
     * Helper Prev
     */
    public function testHelperPrevReturnNull()
    {
        $grid = new ArrayGrid();
        $this->assertNull($grid->prev());
    }

    /**
     * Helper Reset
     */
    public function testHelperReset()
    {
        $grid = new ArrayGrid();
        $this->assertEquals('/', $grid->reset());
    }

    /**
     * Helper Total
     */
    public function testHelperTotal()
    {
        $grid = new ArrayGrid();
        $this->assertEquals(10, $grid->total());
    }
}
