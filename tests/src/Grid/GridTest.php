<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid;

use Bluz\Grid\Source\ArraySource;
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
     * Array Source
     */
    public function testArrayGrid()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals(3, $grid->pages());
        $this->assertEquals(10, $grid->total());
    }

    /**
     * Array Source Exception
     * @expectedException \Bluz\Grid\GridException
     */
    public function testArraySourceThrowsGridException()
    {
        $adapter = new ArraySource();
        $adapter->setSource('wrong source type');
    }

    /**
     * SQL Source
     */
    public function testSqlGrid()
    {
        $grid = new Test\SqlGrid();
        $this->assertEquals(5, $grid->pages());
        $this->assertEquals(42, $grid->total());
    }

    /**
     * SQL Source Exception
     * @expectedException \Bluz\Grid\GridException
     */
    public function testSqlSourceThrowsGridException()
    {
        $adapter = new SqlSource();
        $adapter->setSource(['wrong source type']);
    }

    /**
     * Select Source
     */
    public function testSelectGrid()
    {
        $grid = new Test\SelectGrid();
        $this->assertEquals(5, $grid->pages());
        $this->assertEquals(42, $grid->total());
    }

    /**
     * Select Source Exception
     * @expectedException \Bluz\Grid\GridException
     */
    public function testSelectSourceThrowsGridException()
    {
        $adapter = new SelectSource();
        $adapter->setSource('wrong source type');
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
