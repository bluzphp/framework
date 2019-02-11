<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Grid\Source;

use Bluz\Grid\Grid;
use Bluz\Grid\Source\SqlSource;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Grid\SqlGrid;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class SqlSourceTest extends FrameworkTestCase
{
    /**
     * SQL Source
     */
    public function testSqlGrid()
    {
        $grid = new SqlGrid();
        $grid->setDefaultOrder('id', Grid::ORDER_DESC);
        $grid->addFilter('id', Grid::FILTER_GT, 1);  // id > 1
        $grid->addFilter('email', Grid::FILTER_LIKE, '@');
        self::assertEquals(5, $grid->pages());
        self::assertEquals(41, $grid->total());
    }

    /**
     * SQL Source Exception
     *
     * @expectedException \Bluz\Grid\GridException
     */
    public function testSqlSourceThrowsGridException()
    {
        $adapter = new SqlSource();
        $adapter->setSource(['wrong source type']);
    }
}
