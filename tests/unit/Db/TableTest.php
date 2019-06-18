<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Db;

use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Table;
use Bluz\Tests\Fixtures\Db;
use Bluz\Tests\Fixtures\Models\Test\Table as TestTable;
use Bluz\Tests\FrameworkTestCase;

/**
 * Test class for Table.
 * Generated by PHPUnit on 2011-07-27 at 13:52:47.
 */
class TableTest extends FrameworkTestCase
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        self::getApp();
        $this->table = Db\ConcreteTable::getInstance();
    }

    /**
     * testGetInstance
     *
     * @covers \Bluz\Db\Table::getInstance
     */
    public function testGetInstance()
    {
        // test that the method doesn't create new objects
        self::assertSame($this->table, Db\ConcreteTable::getInstance());

        // tests that instances are creating separately for each table class
        self::assertEquals(
            Db\ConcreteTable::class,
            get_class(Db\ConcreteTable::getInstance())
        );
        self::assertEquals(
            Db\WrongKeysTable::class,
            get_class(Db\WrongKeysTable::getInstance())
        );
    }

    public function testGetPrimaryKeyException()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $table = Db\WrongKeysTable::getInstance();
        $table->getPrimaryKey();
    }

    /**
     * Get Primary Key
     */
    public function testGetPrimaryKey()
    {
        $table = Db\ConcreteTable::getInstance();
        self::assertEquals(['bar', 'baz'], $table->getPrimaryKey());
    }

    /**
     * Get Model name
     */
    public function testGetModel()
    {
        $table = Db\ConcreteTable::getInstance();
        self::assertEquals('Db', $table->getModel());
    }

    /**
     * @dataProvider getFindWrongData
     *
     * @param $keyValues
     */
    public function testFindException($keyValues)
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table::find(...$keyValues);
    }

    /**
     * Get Meta Information
     */
    public function testGetMetaInformation()
    {
        $meta = TestTable::getMeta();
        self::assertArrayHasSize($meta, 6);
        self::assertArrayHasKey('id', $meta);
        self::assertArrayHasKey('name', $meta);
        self::assertEqualsArray(['type' => 'int', 'default' => '', 'key' => 'PRI'], $meta['id']);
    }

    /**
     * Get Meta Information
     */
    public function testGetColumns()
    {
        $columns = TestTable::getColumns();
        self::assertArrayHasSize($columns, 6);
        self::assertEqualsArray(['id', 'name', 'email', 'status', 'created', 'updated'], $columns);
    }

    /**
     * @return array
     */
    public function getFindWrongData()
    {
        return [
            [[1]],
            [[1, 2, 3]]
        ];
    }

    public function testInsert()
    {
        $result = TestTable::getInstance()::insert(['name' => 'Tester', 'email' => 'Very.Strong.Unique@mail.us']);
        self::assertTrue($result > 0);
    }

    public function testFindRowWhere()
    {
        $result = TestTable::findRowWhere(['email' => 'Very.Strong.Unique@mail.us']);
        self::assertEquals('Tester', $result->name);
    }

    public function testNotFoundRowWhere()
    {
        $result = TestTable::findRowWhere(['email' => 'Not.Found@mail.us']);
        self::assertNull($result);
    }

    public function testUpdate()
    {
        $result = TestTable::getInstance()::update(['name' => 'Selfer'], ['email' => 'Very.Strong.Unique@mail.us']);
        self::assertTrue(is_int($result));
        self::assertEquals(1, $result);
    }

    public function testDelete()
    {
        $result = TestTable::getInstance()::delete(['email' => 'Very.Strong.Unique@mail.us']);
        self::assertTrue(is_int($result));
        self::assertEquals(1, $result);
    }
}
