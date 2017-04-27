<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Controller\Mapper;

use Bluz\Controller\Controller;
use Bluz\Controller\Mapper\Crud;
use Bluz\Http\RequestMethod;
use Bluz\Tests\Fixtures\Crud\TableCrud;
use Bluz\Tests\Fixtures\Models\Test\Table;
use Bluz\Tests\TestCase;

/**
 * Test for Controller Crud Mapper
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class CrudTest extends TestCase
{
    /**
     * @expectedException \Bluz\Controller\ControllerException
     */
    public function testGetCrudThrowException()
    {
        $crudMapper = new Crud();
        $crudMapper->getCrud();
    }

    public function testGetCrudInstance()
    {
        $crudMapper = new Crud();
        $crudMapper->setCrud(TableCrud::getInstance());
        self::assertInstanceOf(TableCrud::class, $crudMapper->getCrud());
    }

    /**
     * @expectedException \Bluz\Application\Exception\ApplicationException
     */
    public function testGetPrimaryKeyThrowException()
    {
        $crudMapper = new Crud();
        $crudMapper->setCrud(TableCrud::getInstance());
        $crudMapper->getPrimaryKey();
    }

    public function testGetPrimaryKeyWithEmptyRequest()
    {
        $crudMapper = new Crud();
        $table = TableCrud::getInstance();
        $table->setTable(Table::getInstance());
        $crudMapper->setCrud($table);
        self::assertCount(0, $crudMapper->getPrimaryKey());
    }

    public function testGetPrimaryKey()
    {
        self::setRequestParams('test/test', ['id' => 42], [], RequestMethod::GET);
        self::resetRouter();

        $crudMapper = new Crud();
        $table = TableCrud::getInstance();
        $table->setTable(Table::getInstance());
        $crudMapper->setCrud($table);

        $primaryKey = $crudMapper->getPrimaryKey();

        self::assertArrayHasKey('id', $primaryKey);
        self::assertEquals(42, $primaryKey['id']);
    }

    /**
     * @dataProvider dataMethods
     * @param string $method
     */
    public function testMethod($method)
    {
        self::setRequestParams('test/test', [], [], $method);
        self::resetRouter();

        $crudMapper = new Crud();
        $crudMapper->setCrud(TableCrud::getInstance());
        $crudMapper->addMap($method, 'test', 'test');
        $controller = $crudMapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    /**
     * @expectedException \Bluz\Application\Exception\ForbiddenException
     */
    public function testForbiddenMethod()
    {
        self::setRequestParams('test/test', [], [], RequestMethod::GET);
        self::resetRouter();

        $crudMapper = new Crud();
        $crudMapper->setCrud(TableCrud::getInstance());
        $crudMapper->addMap(RequestMethod::GET, 'test', 'test', 'Deny');
        $crudMapper->run();
    }

    /**
     * @return array
     */
    public function dataMethods() : array
    {
        return [
            RequestMethod::GET => [RequestMethod::GET],
            RequestMethod::POST => [RequestMethod::POST],
            RequestMethod::PATCH => [RequestMethod::PATCH],
            RequestMethod::PUT => [RequestMethod::PUT],
            RequestMethod::DELETE => [RequestMethod::DELETE],
        ];
    }
}
