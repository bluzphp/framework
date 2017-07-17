<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Grid;

use Bluz\Grid\Grid;
use Bluz\Proxy\Request;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Grid\ArrayGrid;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class GridTest extends FrameworkTestCase
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
        $request = Request::getInstance();

        $request = $request->withQueryParams(
            [
                'arr-page' => 2, // 2 page
                'arr-limit' => 2, // 2 rows per page
                'arr-order-id' => 'desc',
                'arr-filter-name' => 'ne-Smith',
                'arr-filter-status' => 'disable',
            ]
        );

        Request::setInstance($request);

        $grid = new ArrayGrid();

        self::assertEquals(8, $grid->total());
        self::assertEquals(4, $grid->pages());
    }


    /**
     * Process Request
     */
    public function testRequestWithAliases()
    {
        $request = Request::getInstance();

        $request = $request->withQueryParams(
            [
                'arr-order-index' => 'desc',
            ]
        );

        Request::setInstance($request);

        $grid = new ArrayGrid();

        self::assertEquals(
            '/index/index/arr-order-index/desc/arr-filter-index/1',
            $grid->filter('id', Grid::FILTER_EQ, 1)
        );
    }

    /**
     * Custom Module and Controller
     */
    public function testCustomControllerAndModule()
    {
        $grid = new ArrayGrid();
        $grid->setModule('module');
        $grid->setController('controller');
        self::assertEquals('/module/controller', $grid->reset());
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

        self::assertEqualsArray($orders, $grid->getOrders());
    }

    /**
     * @covers \Bluz\Grid\Grid::getPrefix
     */
    public function testGetPrefix()
    {
        $grid = new ArrayGrid();
        self::assertEquals('arr-', $grid->getPrefix());
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
        self::assertEquals(4, $grid->getDefaultLimit());
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

        self::assertEquals(
            '/index/index/arr-filter-index/ne-1',
            $grid->filter('id', Grid::FILTER_NE, 1)
        );
        self::assertEquals(
            '/index/index/arr-filter-name/ne-Smith/arr-filter-index/1',
            $grid->filter('id', Grid::FILTER_EQ, 1, false)
        );
        self::assertEquals(
            '/index/index/arr-filter-name/ne-Smith/arr-filter-index/ne-1',
            $grid->filter('id', Grid::FILTER_NE, 1, false)
        );
    }

    /**
     * Helper Filter
     */
    public function testHelperWrongFilterColumnReturnNull()
    {
        $grid = new ArrayGrid();
        self::assertNull($grid->filter('not exist', Grid::FILTER_NE, 1));
    }

    /**
     * Helper Filter
     */
    public function testHelperWrongFilterNameReturnNull()
    {
        $grid = new ArrayGrid();
        self::assertNull($grid->filter('id', 'not exist', 1));
    }

    /**
     * Helper First
     */
    public function testHelperFirst()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/', $grid->first());
    }

    /**
     * Helper Last
     */
    public function testHelperLast()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/index/index/arr-page/3', $grid->last());
    }

    /**
     * Helper Limit
     */
    public function testHelperLimit()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/index/index/arr-limit/25', $grid->limit());
    }

    /**
     * Helper Next
     */
    public function testHelperNext()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/index/index/arr-page/2', $grid->next());
    }

    /**
     * Helper Next
     */
    public function testHelperNextReturnNull()
    {
        $grid = new ArrayGrid();
        $grid->setPage(3);
        self::assertNull($grid->next());
    }

    /**
     * Helper Order
     */
    public function testHelperOrder()
    {
        $grid = new ArrayGrid();
        self::assertNull($grid->order('not exists'));
        self::assertEquals('/index/index/arr-order-name/asc', $grid->order('name'));
        self::assertEquals('/index/index/arr-order-name/asc', $grid->order('name', 'asc', 'asc', false));
    }

    /**
     * Helper Order
     */
    public function testHelperOrderReverseCurrentOrder()
    {
        $grid = new ArrayGrid();
        $grid->setOrder('name');
        self::assertEquals('/index/index/arr-order-name/desc', $grid->order('name'));
    }

    /**
     * Helper Page
     */
    public function testHelperPage()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/index/index/arr-page/2', $grid->page(2));
        self::assertNull($grid->page(42));
    }

    /**
     * Helper Pages
     */
    public function testHelperPages()
    {
        $grid = new ArrayGrid();
        self::assertEquals(3, $grid->pages());
    }

    /**
     * Helper Prev
     */
    public function testHelperPrev()
    {
        $grid = new ArrayGrid();
        $grid->setPage(3);
        self::assertEquals('/index/index/arr-page/2', $grid->prev());
    }

    /**
     * Helper Prev
     */
    public function testHelperPrevReturnNull()
    {
        $grid = new ArrayGrid();
        self::assertNull($grid->prev());
    }

    /**
     * Helper Reset
     */
    public function testHelperReset()
    {
        $grid = new ArrayGrid();
        self::assertEquals('/', $grid->reset());
    }

    /**
     * Helper Total
     */
    public function testHelperTotal()
    {
        $grid = new ArrayGrid();
        self::assertEquals(10, $grid->total());
    }
}
