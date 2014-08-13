<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Grid\Source;

use Bluz\Grid\Source\SelectSource;
use Bluz\Tests\TestCase;
use Bluz\Tests\Fixtures\Models\Test;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  07.08.14 14:37
 */
class SelectSourceTest extends TestCase
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
}
