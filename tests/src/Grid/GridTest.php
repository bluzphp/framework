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
use Bluz\Grid\Source\SelectSource;
use Bluz\Grid\Source\SqlSource;
use Bluz\Tests\TestCase;
use Bluz\Tests\Fixtures\Models\Test;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class GridTest extends TestCase
{
    /**
     * Setup Application
     */
    public function setUp()
    {
        parent::setUp();
        $this->getApp();
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongColumnFilterThrowException()
    {
        $grid = new Test\ArrayGrid();
        $grid->addFilter('not exist', Grid::FILTER_EQ, 'not found');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongFilterNameThrowException()
    {
        $grid = new Test\ArrayGrid();
        $grid->addFilter('id', 'not exist', 'not found');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongOrderThrowException()
    {
        $grid = new Test\ArrayGrid();
        $grid->setDefaultOrder('not exist');
    }

    /**
     * @expectedException \Bluz\Grid\GridException
     */
    public function testWrongOrderDirectionThrowException()
    {
        $grid = new Test\ArrayGrid();
        $grid->setDefaultOrder('id', 'not exist');
    }

    /**
     * Helper First
     */
    public function testHelperFirst()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/', $grid->first());
    }

    /**
     * Helper Last
     */
    public function testHelperLast()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/index/index/arr-page/3', $grid->last());
    }

    /**
     * Helper Limit
     */
    public function testHelperLimit()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/index/index/arr-limit/25', $grid->limit());
    }

    /**
     * Helper Next
     */
    public function testHelperNext()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/index/index/arr-page/2', $grid->next());
    }

    /**
     * Helper Order
     */
    public function testHelperOrder()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/index/index/arr-order-name/asc', $grid->order('name'));
    }

    /**
     * Helper Page
     */
    public function testHelperPage()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/index/index/arr-page/2', $grid->page(2));
        $this->assertNull($grid->page(42));
    }

    /**
     * Helper Pages
     */
    public function testHelperPages()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals(3, $grid->pages());
    }

    /**
     * Helper Prev
     */
    public function testHelperPrev()
    {
        $grid = new Test\ArrayGrid();
        $grid->setPage(3);
        $this->assertEquals('/index/index/arr-page/2', $grid->prev());
    }

    /**
     * Helper Reset
     */
    public function testHelperReset()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals('/', $grid->reset());
    }

    /**
     * Helper Total
     */
    public function testHelperTotal()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals(10, $grid->total());
    }
}
