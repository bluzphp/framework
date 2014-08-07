<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid;

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
     * Array Source
     */
    public function testArrayGrid()
    {
        $grid = new Test\ArrayGrid();
        $this->assertEquals(3, $grid->pages());
        $this->assertEquals(10, $grid->total());
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
     * Select Source
     */
    public function testSelectGrid()
    {
        $grid = new Test\SelectGrid();
        $this->assertEquals(5, $grid->pages());
        $this->assertEquals(42, $grid->total());
    }
}
