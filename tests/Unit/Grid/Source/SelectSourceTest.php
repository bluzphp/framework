<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Grid\Source;

use Bluz\Grid\Grid;
use Bluz\Grid\GridException;
use Bluz\Grid\Source\SelectSource;
use Bluz\Tests\Unit\Unit;
use Bluz\Tests\Fixtures\Grid\SelectGrid;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class SelectSourceTest extends Unit
{
    /**
     * Select Source
     */
    public function testSelectGrid()
    {
        $grid = new SelectGrid();
        $grid->setDefaultOrder('id', Grid::ORDER_DESC);
        $grid->addFilter('id', Grid::FILTER_GT, 1);  // id > 1
        $grid->addFilter('email', Grid::FILTER_LIKE, '@');
        self::assertEquals(5, $grid->pages());
        self::assertEquals(41, $grid->total());
    }

    /**
     * Select Source Exception
     */
    public function testSelectSourceThrowsGridException()
    {
        $this->expectException(GridException::class);
        $adapter = new SelectSource();
        $adapter->setSource('wrong source type');
    }
}
