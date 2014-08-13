<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid\Source;

use Bluz\Grid\Grid;
use Bluz\Grid\Source\ArraySource;
use Bluz\Tests\TestCase;
use Bluz\Tests\Fixtures\Models\Test;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class ArraySourceTest extends TestCase
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
     * Array Source Orders
     */
    public function testOrders()
    {
        $grid = new Test\ArrayGrid();
        $grid->setDefaultOrder('id', Grid::ORDER_DESC);
        $grid->getData();

        $this->assertEquals(3, $grid->pages());
        $this->assertEquals(10, $grid->total());
    }

    /**
     * Array Source Filters
     */
    public function testFilters()
    {
        $grid = new Test\ArrayGrid();

        $grid->addFilter('id', Grid::FILTER_GT, 1);  // id > 1
        $grid->addFilter('id', Grid::FILTER_GE, 2);  // id >= 2
        $grid->addFilter('id', Grid::FILTER_LT, 10); // id < 10
        $grid->addFilter('id', Grid::FILTER_LE, 9);  // id <= 9

        $this->assertEquals(2, $grid->pages());
        $this->assertEquals(8, $grid->total());
    }

    /**
     * Array Source Filters
     */
    public function testFilterEqual()
    {
        $grid = new Test\ArrayGrid();

        $grid->addFilter('id', Grid::FILTER_EQ, 1);  // id = 1

        $this->assertEquals(1, $grid->pages());
        $this->assertEquals(1, $grid->total());
    }

    /**
     * Array Source Filters
     */
    public function testFilterNotEqual()
    {
        $grid = new Test\ArrayGrid();

        $grid->addFilter('id', Grid::FILTER_NE, 1);  // id != 1

        $this->assertEquals(3, $grid->pages());
        $this->assertEquals(9, $grid->total());
    }

    /**
     * Array Source Filters
     */
    public function testFilterLike()
    {
        $grid = new Test\ArrayGrid();

        $grid->addFilter('email', Grid::FILTER_LIKE, '^m@');  // preg_match('/^m@/', email)

        $this->assertEquals(1, $grid->pages());
        $this->assertEquals(2, $grid->total());
    }
}
