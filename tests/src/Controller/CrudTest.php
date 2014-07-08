<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Controller;

use Bluz\Http;
use Bluz\Http\Request;
use Bluz\Controller;
use Bluz\Tests\BootstrapTest;
use Bluz\Tests\Fixtures\Models\TestCrud;
use Bluz\Tests\TestCase;

/**
 * @package  Application\Tests\Test
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
        BootstrapTest::getInstance()->getDb()->insert('test')->setArray(
            [
                'id' => 1,
                'name' => 'Donatello',
                'email' => 'donatello@turtles.org'
            ]
        )->execute();

        BootstrapTest::getInstance()->getDb()->insert('test')->setArray(
            [
                'id' => 2,
                'name' => 'Leonardo',
                'email' => 'leonardo@turtles.org'
            ]
        )->execute();

        BootstrapTest::getInstance()->getDb()->insert('test')->setArray(
            [
                'id' => 3,
                'name' => 'Michelangelo',
                'email' => 'michelangelo@turtles.org'
            ]
        )->execute();

        BootstrapTest::getInstance()->getDb()->insert('test')->setArray(
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
        BootstrapTest::getInstance()->getDb()->delete('test')->where('id IN (?)', [1,2,3,4])->execute();
        BootstrapTest::getInstance()->getDb()->delete('test')->where('email = ?', 'splinter@turtles.org')->execute();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->resetApp();
    }

    /**
     * Process Crud
     *
     * @return mixed
     */
    protected function processCrud()
    {
        $crudController = new Controller\Crud();
        $crudController->setCrud(TestCrud::getInstance());
        return $crudController();
    }

    /**
     * GET without PRIMARY should return NEW RECORD
     */
    public function testNewRecord()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_POST, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertNull($result['row']['id']);
    }

    /**
     * GET with PRIMARY should return RECORD
     */
    public function testReadRecord()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setParam('id', 1);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertEquals(1, $result['row']['id']);
    }
    /**
     * GET with invalid PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testReadRecordError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setParam('id', 100042);

        $this->processCrud();
    }

    /**
     * POST request should CREATE record
     */
    public function testCreate()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setParams(['name' => 'Splinter', 'email' => 'splinter@turtles.org']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertNotNull($result['row']['id']);
    }


    /**
     * POST request with empty data should return ERROR and information
     */
    public function testCreateValidationErrors()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setParams(['name' => '', 'email' => '']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_POST, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertNull($result['row']['id']);
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request should UPDATE record
     */
    public function testUpdate()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_PUT);
        $request->setParams(['id' => 2, 'name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertEquals(2, $result['row']['id']);

        $id = $this->getApp()->getDb()->fetchOne(
            'SELECT `id` FROM `test` WHERE `email` = ?',
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
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_PUT);
        $request->setParams(['id' => 100042, 'name' => 'You Knows', 'email' => 'all@turtles.ua']);

        $this->processCrud();
    }

    /**
     * PUT request with invalid data should return ERROR and information
     */
    public function testUpdateValidationErrors()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_PUT);
        $request->setParams(['id' => 2, 'name' => '123456', 'email' => 'leonardo[at]turtles.ua']);


        $result = $this->processCrud();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertEquals(2, $result['row']['id']);
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * DELETE request should remove record
     */
    public function testDelete()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_DELETE);
        $request->setParams(['id' => 3]);

        $result = $this->processCrud();
        $this->assertEquals(1, $result);

        $count = $this->getApp()->getDb()->fetchOne('SELECT count(*) FROM `test` WHERE `id` = ?', [3]);
        $this->assertEquals(0, $count);
    }

    /**
     * DELETE request with invalid id should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testDeleteError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_DELETE);
        $request->setParams(['id' => 100042]);

        $this->processCrud();
    }

    /**
     * HEAD should return EXCEPTION
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedException()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_HEAD);

        $this->processCrud();
    }
}
