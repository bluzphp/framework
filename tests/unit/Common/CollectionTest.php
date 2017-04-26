<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common;

use Bluz\Common\Collection;
use Bluz\Tests\TestCase;

/**
 * Tests for Collection helpers
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  21.04.17 12:36
 */
class CollectionTest extends TestCase
{
    /**
     * @var array
     */
    protected $array;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->array = [
            'hello' => [
                'world',
                'country' => ['Ukraine' => 1, 'Sweden' => 2]
            ],
            'foo' => 'bar',
            1 => 2,
            2 => [1, 2, 3],
            3 => [1, 2, 3 => ['a', 'b', 'c']]
        ];
    }

    /**
     * @return array
     */
    public function dataForCorrectCheck()
    {
        return [
            ['hello'],
            ['hello', 'country'],
            ['hello', 'country', 'Ukraine'],
            [1],
            [2],
            [3],
            [3, 3],
        ];
    }

    /**
     * @return array
     */
    public function dataForIncorrectCheck()
    {
        return [
            ['hi'],
            ['hello', 'city'],
            ['hello', 'country', 'Russia'],
            ['foo', 'bar'],
            [3, 2],
            [4],
        ];
    }

    /**
     * Test has class
     *
     * @dataProvider dataForCorrectCheck
     */
    public function testHasReturnTrue(...$keys)
    {
        self::assertTrue(Collection::has($this->array, ...$keys));
    }

    /**
     * Test has class
     *
     * @dataProvider dataForIncorrectCheck
     */
    public function testHasReturnFalse(...$keys)
    {
        self::assertFalse(Collection::has($this->array, ...$keys));
    }

    /**
     * Test Get method for return values
     */
    public function testGetValue()
    {
        self::assertEquals(1, Collection::get($this->array, 'hello', 'country', 'Ukraine'));
        self::assertEquals(2, Collection::get($this->array, 1));
        self::assertEquals(3, Collection::get($this->array, 2, 2));
    }

    /**
     * @dataProvider dataForIncorrectCheck
     */
    public function testGetNul(...$keys)
    {
        self::assertNull(Collection::get($this->array, ...$keys));
    }

    /**
     * Test Add method for return values
     */
    public function testAddValue()
    {
        Collection::add($this->array, 'hello', 'city', 'Kyiv');
        Collection::add($this->array, 'hello', 'city', 'Kharkiv');
        Collection::add($this->array, 2, 4);
        Collection::add($this->array, 3, 'd');

        self::assertCount(2, Collection::get($this->array, 'hello', 'city'));
        self::assertCount(4, Collection::get($this->array, 2));
        self::assertCount(3, Collection::get($this->array, 3, 3));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddWithoutArguments()
    {
        Collection::add($this->array);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddWithoutValue()
    {
        Collection::add($this->array, 'hello');
    }

    /**
     * Test for helper function array_set
     */
    public function testArrayAddFunction()
    {
        array_add($this->array, 'ukraine', 'city', 'Kyiv');
        array_add($this->array, 'ukraine', 'city', 'Kharkiv');

        self::assertCount(2, $this->array['ukraine']['city']);
    }

    /**
     * Test Set method for return values
     */
    public function testSetValue()
    {
        Collection::set($this->array, 'hello', 'city', 'Kharkiv', 'point');
        Collection::set($this->array, 'hello', 'country', 'Ukraine', 'Kyiv');
        Collection::set($this->array, 1, 0);
        Collection::set($this->array, 2, [42]);

        self::assertEquals('point', Collection::get($this->array, 'hello', 'city', 'Kharkiv'));
        self::assertEquals('Kyiv', Collection::get($this->array, 'hello', 'country', 'Ukraine'));
        self::assertEquals(0, Collection::get($this->array, 1));
        self::assertEquals(42, Collection::get($this->array, 2, 0));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithoutArguments()
    {
        Collection::set($this->array);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithoutValue()
    {
        Collection::set($this->array, 'hello');
    }

    /**
     * Test for helper function array_set
     */
    public function testArraySetFunction()
    {
        array_set($this->array, 'hello', 'country', 'Ukraine', 'Kyiv', 'Yes!');

        self::assertTrue(isset($this->array['hello']['country']['Ukraine']['Kyiv']));
    }

    /**
     * Test for helper function array_has
     */
    public function testArrayHasFunction()
    {
        self::assertTrue(array_has($this->array, 'hello', 'country', 'Ukraine'));
    }

    /**
     * Test for helper function array_get
     */
    public function testArrayGetFunction()
    {
        self::assertEquals(1, array_get($this->array, 'hello', 'country', 'Ukraine'));
    }
}
