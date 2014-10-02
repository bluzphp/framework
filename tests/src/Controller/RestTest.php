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
use Bluz\Proxy\Response;
use Bluz\Proxy\Request;
use Bluz\Tests\Fixtures\Models\Test\Crud;
use Bluz\Tests\TestCase;

/**
 * @package  Bluz\Tests
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
     * Process Rest
     *
     * @return mixed
     */
    protected function processRest()
    {
        $restController = new Controller\Rest();
        $restController->setCrud(Crud::getInstance());
        return $restController();
    }

    /**
     * GET with PRIMARY should return RECORD
     */
    public function testReadOne()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setRawParams([1]);

        $result = $this->processRest();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $row);
        $this->assertEquals(1, $row['id']);
    }

    /**
     * GET with PRIMARY and RELATION should return SET of RELATIONS
     * @todo realization
     */
    public function testReadOneWithRelations()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setRawParams([1, 'pages']);

        $result = $this->processRest();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $row);
        $this->assertEquals(1, $row['id']);
    }

    /**
     * GET with PRIMARY, RELATION and PRIMARY OF RELATION should return RECORD
     * @todo realization
     */
    public function testReadOneWithOneRelation()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setRawParams([1, 'pages', 1]);

        $result = $this->processRest();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $row);
        $this->assertEquals(1, $row['id']);
    }

    /**
     * GET with invalid PRIMARY should return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testReadOneError()
    {
        Request::setMethod(Request::METHOD_GET);
        Request::setRawParams([100042]);

        $this->processRest();
    }

    /**
     * GET request should return SET of records
     */
    public function testReadSet()
    {
        Request::setMethod(Request::METHOD_GET);

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
        Request::setMethod(Request::METHOD_GET);

        $_SERVER['HTTP_RANGE'] = 'test=0-3';

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * POST request with params should CREATE row and return PRIMARY
     */
    public function testCreate()
    {
        Request::setMethod(Request::METHOD_POST);
        Request::setParams(['name' => 'Splinter', 'email' => 'splinter@turtles.org']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $primary = Db::fetchOne(
            'SELECT id FROM test WHERE name = ?',
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
        Request::setMethod(Request::METHOD_POST);
        Request::setRawParams([1]);

        $this->processRest();
    }

    /**
     * POST request without DATA should return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testCreateWithoutDataError()
    {
        Request::setMethod(Request::METHOD_POST);

        $this->processRest();
    }

    /**
     * POST request with invalid data should return ERROR and information
     */
    public function testCreateValidationErrors()
    {
        Request::setMethod(Request::METHOD_POST);
        Request::setParams(['name' => '', 'email' => '']);

        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with PRIMARY should UPDATE record
     */
    public function testUpdate()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setRawParams([2]);
        Request::setParams(['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $id = Db::fetchOne(
            'SELECT id FROM test WHERE email = ?',
            ['leonardo@turtles.ua']
        );
        $this->assertEquals($id, 2);
    }

    /**
     * PUT request with PRIMARY should UPDATE record
     */
    public function testUpdateWithSameDate()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setRawParams([1]);
        Request::setParams(['name' => 'Donatello', 'email' => 'donatello@turtles.org']);

        $result = $this->processRest();
        $this->assertFalse($result);

        $this->assertEquals(304, Response::getStatusCode());
    }

    /**
     * PUT request with invalid PRIMARY return ERROR
     * @expectedException \Bluz\Application\Exception\NotFoundException
     */
    public function testUpdateNotFoundError()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setRawParams([100042]);
        Request::setParams(['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua']);

        $this->processRest();
    }

    /**
     * PUT request with invalid DATA return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testUpdateNotDataError()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setRawParams([100042]);

        $this->processRest();
    }

    /**
     * PUT request with invalid data should return ERROR
     */
    public function testUpdateValidationErrors()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setRawParams([2]);
        Request::setParams(['name' => '123456', 'email' => 'leonardo[at]turtles.ua']);

        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with SET of DATA should UPDATE SET
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testUpdateSet()
    {
        Request::setMethod(Request::METHOD_PUT);
        Request::setParams(
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
        Request::setMethod(Request::METHOD_DELETE);
        Request::setRawParams([1]);

        $result = $this->processRest();
        $this->assertFalse($result);

        $count = Db::fetchOne(
            'SELECT count(*) FROM test WHERE id = ?',
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
        Request::setMethod(Request::METHOD_DELETE);
        Request::setRawParams([100042]);

        $this->processRest();
    }

    /**
     * DELETE request without PRIMARY and DATA should return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testDeleteWithoutData()
    {
        Request::setMethod(Request::METHOD_DELETE);

        $this->processRest();
    }

    /**
     * DELETE request with SET of DATA should DELETE SET but not implemented yet
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testDeleteSet()
    {
        Request::setMethod(Request::METHOD_DELETE);
        Request::setParams(
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
        Request::setMethod(Request::METHOD_HEAD);

        $this->processRest();
    }
}
