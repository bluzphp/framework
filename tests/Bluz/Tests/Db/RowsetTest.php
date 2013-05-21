<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Db;

use Bluz;
use Bluz\Db;
use Bluz\Tests\Db\Fixtures;

/**
 * Test class for Rowset.
 * Generated by PHPUnit on 2011-07-27 at 13:52:00.
 */
class RowsetTest extends Bluz\Tests\TestCase
{

    /**
     * @var \Bluz\Db\Rowset
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Bluz\Db\Rowset([0,1,null]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Bluz\Db\Rowset::rewind
     */
    public function testRewind()
    {
        $this->object->rewind();
        $this->assertEquals(0, $this->object->key());
    }

    /**
     * @covers Bluz\Db\Rowset::current
     */
    public function testCurrentOfEmptyRowset()
    {
        $currentRow = $this->object->current();
        $this->assertEquals(null, $currentRow);
    }

    /**
     * @covers Bluz\Db\Rowset::current
     */
    public function testCurrentOfNotEmptyRowset()
    {
        $currentRow = $this->object->current();
        $this->assertEquals(null, $currentRow);
    }

    /**
     * @covers Bluz\Db\Rowset::key
     */
    public function testKey()
    {
        $this->assertEquals(0, $this->object->key());
    }

    /**
     * @covers Bluz\Db\Rowset::next
     */
    public function testNext()
    {
        $this->object->next();
        $this->assertEquals(1, $this->object->key());
    }

    /**
     * @covers Bluz\Db\Rowset::valid
     */
    public function testValidEmptyRowset() {
        $this->object = new Bluz\Db\Rowset();
        $this->assertEquals(false, $this->object->valid());
    }

    /**
     * @covers Bluz\Db\Rowset::valid
     */
    public function testValidNotEmpty() {
        $this->object = new Bluz\Db\Rowset(
            array(
                new Bluz\Tests\Db\Fixtures\ConcreteRow(),
                new Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable()
            )
        );
        $this->assertEquals(true, $this->object->valid());
    }

    /**
     * @covers Bluz\Db\Rowset::count
     */
    public function testCountEmptyRowset()
    {
        $this->object = new Bluz\Db\Rowset();
        $this->assertEquals(0, $this->object->count());
    }

    /**
     * @covers Bluz\Db\Rowset::count
     */
    public function testCountNotEmptyRowset()
    {
        $this->object = new Bluz\Db\Rowset(
            array(
                new Bluz\Tests\Db\Fixtures\ConcreteRow(),
                new Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable()
            )
        );
        $this->assertEquals(2, $this->object->count());
    }

    /**
     * @covers Bluz\Db\Rowset::seek
     * @expectedException \OutOfBoundsException
     */
    public function testSeekOutOfBoundsException()
    {
        $this->object->seek(42);
    }

    /**
     * @covers Bluz\Db\Rowset::seek
     */
    public function testSeek()
    {
        $this->object = new Bluz\Db\Rowset(
            array(
                new Bluz\Tests\Db\Fixtures\ConcreteRow(),
                new Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable()
            )
        );
        $this->object->seek(1);
        $this->assertEquals(1, $this->object->key());
    }

    /**
     * @covers Bluz\Db\Rowset::offsetExists
     */
    public function testOffsetExists()
    {
        $this->object = new Bluz\Db\Rowset(
            array(
                new Bluz\Tests\Db\Fixtures\ConcreteRow(),
                new Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable()
            )
        );
        $this->object->seek(1);
        $this->assertTrue($this->object->offsetExists(1));
    }

    /**
     * @covers Bluz\Db\Rowset::offsetExists
     */
    public function testOffsetExistsException()
    {
        $this->object = new Bluz\Db\Rowset();
        $this->assertFalse($this->object->offsetExists(1));
    }

    /**
     * @covers Bluz\Db\Rowset::offsetGet
     */
    public function testOffsetGet()
    {
        $this->object = new Bluz\Db\Rowset(
            array(
                new Bluz\Tests\Db\Fixtures\ConcreteRow(),
                new Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable()
            )
        );
        $this->assertInstanceOf(
            'Bluz\Tests\Db\Fixtures\ConcreteRowWithInvalidTable',
            $this->object->offsetGet(1)
        );
    }
}
