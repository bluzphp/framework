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

        self::getApp();
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
     * HEAD with PRIMARY should return just headers
     */
    public function testOverviewOne()
    {
        $this->setRequestParams(
            ['1'],
            [],
            Request::METHOD_HEAD,
            'index/index'
        );

        $result = $this->processRest();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\Test\Row', $row);
        $this->assertEquals(1, $row['id']);
    }

    /**
     * HEAD with PRIMARY should return just headers
     */
    public function testOverviewSet()
    {
        $this->setRequestParams(
            ['offset' => 0, 'limit' => 3],
            [],
            Request::METHOD_HEAD,
            'index/index'
        );

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * GET with PRIMARY should return RECORD
     */
    public function testReadOne()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_GET,
            'index/index/1'
        );

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
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_GET,
            'index/index/1/pages'
        );

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
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_GET,
            'index/index/1/pages/1'
        );

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
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_GET,
            'index/index/100042'
        );

        $this->processRest();
    }

    /**
     * GET request should return SET of records
     */
    public function testReadSet()
    {
        $this->setRequestParams(
            ['offset' => 0, 'limit' => 3],
            [],
            Request::METHOD_HEAD,
            'index/index'
        );

        $restController = new Controller\Rest();
        $restController->setCrud(Crud::getInstance());

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * GET request should return SET of records
     */
    public function testReadSetWithRange()
    {
        $request = $this->setRequestParams(
            [],
            [],
            Request::METHOD_GET,
            'index/index'
        );

        $request = $request->withHeader('Range', 'test=0-3');
        Request::setInstance($request);

        $result = $this->processRest();

        $this->assertEquals(sizeof($result), 3);
    }

    /**
     * POST request with params should CREATE row and return PRIMARY
     */
    public function testCreate()
    {
        $this->setRequestParams(
            [],
            ['name' => 'Splinter', 'email' => 'splinter@turtles.org'],
            Request::METHOD_POST,
            'index/index'
        );

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
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_POST,
            'index/index/1'
        );

        $this->processRest();
    }

    /**
     * POST request without DATA should return ERROR and information
     */
    public function testCreateWithoutDataError()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_POST,
            'index/index'
        );

        $this->processRest();
        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * POST request with invalid data should return ERROR and information
     */
    public function testCreateValidationErrors()
    {
        $this->setRequestParams(
            [],
            ['name' => '', 'email' => ''],
            Request::METHOD_POST,
            'index/index'
        );
        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with PRIMARY should UPDATE record
     */
    public function testUpdate()
    {
        $this->setRequestParams(
            [],
            ['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua'],
            Request::METHOD_PUT,
            'index/index/2'
        );

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
        $this->setRequestParams(
            [],
            ['name' => 'Donatello', 'email' => 'donatello@turtles.org'],
            Request::METHOD_PUT,
            'index/index/1'
        );

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
        $this->setRequestParams(
            [],
            ['name' => 'Leonardo', 'email' => 'leonardo@turtles.ua'],
            Request::METHOD_PUT,
            'index/index/100042'
        );

        $this->processRest();
    }

    /**
     * PUT request with invalid DATA return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testUpdateNotDataError()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_PUT,
            'index/index/100042'
        );

        $this->processRest();
    }

    /**
     * PUT request with invalid data should return ERROR
     */
    public function testUpdateValidationErrors()
    {
        $this->setRequestParams(
            [],
            ['name' => '123456', 'email' => 'leonardo[at]turtles.ua'],
            Request::METHOD_PUT,
            'index/index/2'
        );

        $result = $this->processRest();
        $this->assertEquals(sizeof($result['errors']), 2);
    }

    /**
     * PUT request with SET of DATA should UPDATE SET
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testUpdateSet()
    {

        $this->setRequestParams(
            [],
            [
                ['id' => 3, 'name' => 'Michelangelo', 'email' => 'michelangelo@turtles.org.ua'],
                ['id' => 4, 'name' => 'Raphael', 'email' => 'Raphael@turtles.org.ua'],
            ],
            Request::METHOD_PUT,
            'index/index'
        );

        $this->processRest();
    }

    /**
     * DELETE request with PRIMARY
     */
    public function testDelete()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_DELETE,
            'index/index/1'
        );

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
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_DELETE,
            'index/index/100042'
        );

        $this->processRest();
    }

    /**
     * DELETE request without PRIMARY and DATA should return ERROR
     * @expectedException \Bluz\Application\Exception\BadRequestException
     */
    public function testDeleteWithoutData()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_DELETE,
            'index/index'
        );

        $this->processRest();
    }

    /**
     * DELETE request with SET of DATA should DELETE SET but not implemented yet
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testDeleteSet()
    {
        $this->setRequestParams(
            [],
            [
                ['id' => 3],
                ['id' => 4],
            ],
            Request::METHOD_DELETE,
            'index/index'
        );

        $this->processRest();
    }

    /**
     * OPTIONS request should set Allow header
     */
    public function testOptionsOne()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_OPTIONS,
            'index/index/100042'
        );

        $this->processRest();

        $this->assertEquals('HEAD,OPTIONS,GET,PATCH,PUT,DELETE', Response::getHeader('Allow'));
    }

    /**
     * OPTIONS request should set Allow header
     */
    public function testOptionsSet()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_OPTIONS,
            'index/index'
        );

        $this->processRest();

        $this->assertEquals('HEAD,OPTIONS,GET,POST', Response::getHeader('Allow'));
    }

    /**
     * HEAD should return EXCEPTION
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedException()
    {
        $this->setRequestParams(
            [],
            [],
            Request::METHOD_TRACE,
            'index/index'
        );

        $this->processRest();
    }
}
