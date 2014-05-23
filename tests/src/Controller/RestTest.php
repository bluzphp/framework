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
class RestTest extends TestCase
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
     * Process Rest
     *
     * @return mixed
     */
    protected function processRest()
    {
        $restController = new Controller\Rest();
        $restController->setCrud(TestCrud::getInstance());
        return $restController();
    }

    /**
     * GET with PRIMARY should return RECORD
     */
    public function testReadOne()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setRawParams([1]);

        $result = $this->processRest();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $row);
        $this->assertEquals(1, $row['id']);
    }

    /**
     * GET with invalid PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testReadOneError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setRawParams([100042]);

        $this->processRest();
    }

    /**
     * GET request should return SET of records
     */
    public function testReadSet()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);

        $_GET['offset'] = 0;
        $_GET['limit'] = 3;

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * GET request should return SET of records
     */
    public function testReadSetWithRange()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);

        $_SERVER['HTTP_RANGE'] = 'test=0-3';

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * POST request with params should CREATE row and return PRIMARY
     */
    public function testCreate()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setParams(['name' => 'Splinter', 'email' => 'splinter@turtles.org']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $primary = $this->getApp()->getDb()->fetchOne(
            'SELECT id FROM `test` WHERE `name` = ?',
            ['Splinter']
        );

        $this->assertNotNull($primary);
    }

    /**
     * POST request with PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testCreateWithPrimaryError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setRawParams([1]);

        $this->processRest();
    }

    /**
     * POST request without DATA should return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testCreateWithoutDataError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);

        $this->processRest();
    }

    /**
     * POST request with invalid data should return ERROR and information
     */
    public function testCreateValidationErrors()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setParams(['name' => '', 'email' => '']);

        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with PRIMARY should UPDATE record
     */
    public function testUpdate()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setRawParams([2]);
        $request->setParams(['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $id = $this->getApp()->getDb()->fetchOne(
            'SELECT `id` FROM `test` WHERE `email` = ?',
            ['leonardo@turtles.ua']
        );
        $this->assertEquals($id, 2);
    }

    /**
     * PUT request with PRIMARY should UPDATE record
     */
    public function testUpdateWithSameDate()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setRawParams([1]);
        $request->setParams(['name' => 'Donatello', 'email' => 'donatello@turtles.org']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $response = $this->getApp()->getResponse();
        $this->assertEquals(304, $response->getCode());
    }

    /**
     * PUT request with invalid PRIMARY return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testUpdateNotFoundError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setRawParams([100042]);
        $request->setParams(['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $this->processRest();
    }

    /**
     * PUT request with invalid DATA return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testUpdateNotDataError()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setRawParams([100042]);

        $this->processRest();
    }

    /**
     * PUT request with invalid data should return ERROR
     */
    public function testUpdateValidationErrors()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setRawParams([2]);
        $request->setParams(['name' => '123456', 'email' => 'leonardo[at]turtles.ua']);

        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with SET of DATA should UPDATE SET
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testUpdateSet()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_PUT);
        $request->setParams(
            [
                ['id' => 3, 'name' => 'Michelangelo', 'email' => 'michelangelo@turtles.org.ua'],
                ['id' => 4, 'name' => 'Raphael', 'email' => 'Raphael@turtles.org.ua'],
            ]
        );

        $this->processRest();
    }

    /**
     * DELETE request with PRIMARY
     */
    public function testDelete()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setRawParams([1]);

        $result = $this->processRest();
        $this->assertFalse($result);

        $count = $this->getApp()->getDb()->fetchOne(
            'SELECT count(*) FROM `test` WHERE `id` = ?',
            [1]
        );
        $this->assertEquals($count, 0);
    }

    /**
     * DELETE request with invalid PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testDeleteWithInvalidPrimary()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setRawParams([100042]);

        $this->processRest();
    }

    /**
     * DELETE request without PRIMARY and DATA should return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testDeleteWithoutData()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_DELETE);

        $this->processRest();
    }

    /**
     * DELETE request with SET of DATA should DELETE SET but not implemented yet
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testDeleteSet()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setParams(
            [
                ['id' => 3],
                ['id' => 4],
            ]
        );

        $this->processRest();
    }

    /**
     * HEAD should return EXCEPTION
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedException()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Http\Request::METHOD_HEAD);

        $this->processRest();
    }
}
