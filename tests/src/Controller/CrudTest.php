<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Controller;

use Bluz\Controller;
use Bluz\Proxy\Db;
use Bluz\Proxy\Request;
use Bluz\Tests\Fixtures\Models\Test\Crud;
use Bluz\Tests\TestCase;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  21.05.14 11:28
 */
class CrudTest extends TestCase
{
    /**
     * Setup `test` table before the first test
     */
    public static function setUpBeforeClass()
    {
        Db::insert('test')->setArray(
            [
                'id' => 1,
                'name' => 'Donatello',
                'email' => 'donatello@turtles.org'
            ]
        )->execute();

        Db::insert('test')->setArray(
            [
                'id' => 2,
                'name' => 'Leonardo',
                'email' => 'leonardo@turtles.org'
            ]
        )->execute();

        Db::insert('test')->setArray(
            [
                'id' => 3,
                'name' => 'Michelangelo',
                'email' => 'michelangelo@turtles.org'
            ]
        )->execute();

        Db::insert('test')->setArray(
            [
                'id' => 4,
                'name' => 'Raphael',
                'email' => 'raphael@turtles.org'
            ]
        )->execute();
    }

    /**
     * Drop `test` table after the last test
     */
    public static function tearDownAfterClass()
    {
        Db::delete('test')->where('id IN (?)', [1, 2, 3, 4])->execute();
        Db::delete('test')->where('email = ?', 'splinter@turtles.org')->execute();

        self::resetGlobals();
        self::resetApp();
    }

    /**
     * Process Crud
     *
     * @return mixed
     */
    protected function processCrud()
    {
        $crudController = new Controller\Crud();
        $crudController->setCrud(Crud::getInstance());
        return $crudController();
    }

    /**
     * GET without PRIMARY should return NEW RECORD
     */
    public function testNewRecord()
    {
        Request::setMethod(Request::METHOD_GET);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_POST, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertNull($result['row']['id']);
    }

    /**
     * GET with PRIMARY should return RECORD
     */
    public function testReadRecord()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setParam('id', 1);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertEquals(1, $result['row']['id']);
    }
    /**
     * GET with invalid PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testReadRecordError()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setParam('id', 100042);

        $this->processCrud();
    }

    /**
     * POST request should CREATE record
     */
    public function testCreate()
    {
        Request::setMethod(Request::METHOD_POST);
        Request::setParams(['name' => 'Splinter', 'email' => 'splinter@turtles.org']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertNotNull($result['row']['id']);
    }


    /**
     * POST request with empty data should return ERROR and information
     */
    public function testCreateValidationErrors()
    {
        Request::setMethod(Request::METHOD_POST);
        Request::setParams(['name' => '', 'email' => '']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_POST, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertNull($result['row']['id']);
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request should UPDATE record
     */
    public function testUpdate()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setParams(['id' => 2, 'name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertEquals(2, $result['row']['id']);

        $id = Db::fetchOne(
            'SELECT id FROM test WHERE email = ?',
            ['leonardo@turtles.ua']
        );
        $this->assertEquals($id, 2);
    }

    /**
     * PUT request with invalid PRIMARY return ERROR and information
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testUpdateNotFoundError()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setParams(['id' => 100042, 'name' => 'You Knows', 'email' => 'all@turtles.ua']);

        $this->processCrud();
    }

    /**
     * PUT request with invalid data should return ERROR and information
     */
    public function testUpdateValidationErrors()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setParams(['id' => 2, 'name' => '123456', 'email' => 'leonardo[at]turtles.ua']);


        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $result['row']);
        $this->assertEquals(2, $result['row']['id']);
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * DELETE request should remove record
     */
    public function testDelete()
    {
        Request::setMethod(Request::METHOD_DELETE);
        Request::setParams(['id' => 3]);

        $result = $this->processCrud();
        $this->assertEquals(1, $result);

        $count = Db::fetchOne('SELECT count(*) FROM test WHERE id = ?', [3]);
        $this->assertEquals(0, $count);
    }

    /**
     * DELETE request with invalid id should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testDeleteError()
    {
        Request::setMethod(Request::METHOD_DELETE);
        Request::setParams(['id' => 100042]);

        $this->processCrud();
    }

    /**
     * HEAD should return EXCEPTION
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedException()
    {
        Request::setMethod(Request::METHOD_HEAD);

        $this->processCrud();
    }
}
