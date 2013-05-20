<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
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
use Bluz\Tests;
use Bluz\Db;
use Bluz\Tests\Db\Fixtures;

/**
 * Test class for Table.
 * Generated by PHPUnit on 2011-07-27 at 13:52:47.
 */
class TableTest extends Bluz\Tests\TestCase
{
    /**
     * @var Bluz\Db\Table
     */
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->table = Bluz\Tests\Db\Fixtures\ConcreteTable::getInstance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * testGetInstance
     * @covers Bluz\Db\Table::getInstance
     */
    public function testGetInstance()
    {
        // test that the method doesn't create new objects
        $this->assertSame($this->table, Bluz\Tests\Db\Fixtures\ConcreteTable::getInstance());
        
        // tests that instances are creating separately for each table class
        $this->assertEquals(
            'Bluz\Tests\Db\Fixtures\ConcreteTable',
            get_class(Bluz\Tests\Db\Fixtures\ConcreteTable::getInstance())
        );
        $this->assertEquals(
            'Bluz\Tests\Db\Fixtures\WrongKeysTable',
            get_class(Bluz\Tests\Db\Fixtures\WrongKeysTable::getInstance())
        );
    }

    /**
     * @covers Bluz\Db\Table::setAdapter
     * @expectedException Bluz\Db\DbException
     */
    public function testSetAdapterWithoutConfig()
    {
        $this->table->setAdapter();
    }

    /**
     * @covers Bluz\Db\Table::setAdapter
     * @expectedException Bluz\Db\DbException
     */
    public function testGetAdapterWithoutConfig()
    {
        $this->table->getAdapter();
    }
    
    /**
     * @covers Bluz\Db\Table::getAdapter
     * @expectedException Bluz\Db\InvalidPrimaryKeyException
     */
    public function testGetPrimaryKeyException()
    {
        $table = Bluz\Tests\Db\Fixtures\WrongKeysTable::getInstance();
        $table->getPrimaryKey();
    }

    /**
     * @covers Bluz\Db\Table::getAdapter
     */
    public function testGetPrimaryKey()
    {
        $table = Bluz\Tests\Db\Fixtures\ConcreteTable::getInstance();
        $this->assertEquals(array('bar', 'baz'), $table->getPrimaryKey());
    }

    /**
     * @dataProvider getFindWrongData
     * @expectedException Bluz\Db\InvalidPrimaryKeyException
     * @param $keyValues
     */
    public function testFindException($keyValues)
    {
        call_user_func_array(array($this->table, 'find'), $keyValues);
    }

    /**
     * @return array
     */
    public function getFindWrongData()
    {
        return array(
            array(array(1)),
            array(array(1, 2, 3))
        );
    }

    /**
     * @todo Implement testFind().
     */
    public function testFind()
    {
        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testInsert().
     */
    public function testInsert()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testUpdate().
     */
    public function testUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}