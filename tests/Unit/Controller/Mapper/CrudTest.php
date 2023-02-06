<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Controller\Mapper;

use Bluz\Controller\Controller;
use Bluz\Controller\Mapper\Crud;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\RequestMethod;
use Bluz\Tests\Fixtures\Crud\TableCrud;
use Bluz\Tests\Fixtures\Models\Test\Table;
use Bluz\Tests\Unit\Unit;

/**
 * Test for Controller Crud Mapper
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class CrudTest extends Unit
{
    /**
     * @var Crud
     */
    protected Crud $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $crudTable = TableCrud::getInstance();
        $crudTable->setTable(Table::getInstance());
        $this->mapper = new Crud($crudTable);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        TableCrud::getInstance()->resetTable();
    }

    public function testCheckGetRequestWithPrimary()
    {
        self::setRequestParams('test/mapper', ['id' => 42], [], RequestMethod::GET);

        $this->mapper->addMap(RequestMethod::GET, 'test', 'mapper');

        $controller = $this->mapper->run();

        $data = $controller->getData();

        self::assertArrayHasKey('primary', $data);
        self::assertArrayHasKey('id', $data['primary']);
        self::assertEquals(42, $data['primary']['id']);
    }

    /**
     * @dataProvider dataMethods
     *
     * @param RequestMethod $method
     */
    public function testMethods(RequestMethod $method)
    {
        self::setRequestParams('test/index', [], [], $method);
        $this->mapper->addMap($method, 'test', 'index');
        $controller = $this->mapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    /**
     * @dataProvider dataMethods
     *
     * @param RequestMethod $method
     */
    public function testMethodsAliases(RequestMethod $method)
    {
        self::setRequestParams('test/index', [], [], $method);

        $alias = strtolower($method->value);

        $this->mapper->$alias('test', 'index');

        $controller = $this->mapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    public function testForbiddenMethod()
    {
        $this->expectException(ForbiddenException::class);
        self::setRequestParams('test/index', [], [], RequestMethod::GET);

        $this->mapper->addMap(RequestMethod::GET, 'test', 'index')->acl('Deny');
        $this->mapper->run();
    }

    /**
     * @return array
     */
    public function dataMethods(): array
    {
        return [
            RequestMethod::HEAD->value => [RequestMethod::HEAD],
            RequestMethod::GET->value => [RequestMethod::GET],
            RequestMethod::POST->value => [RequestMethod::POST],
            RequestMethod::PATCH->value => [RequestMethod::PATCH],
            RequestMethod::PUT->value => [RequestMethod::PUT],
            RequestMethod::DELETE->value => [RequestMethod::DELETE],
            RequestMethod::OPTIONS->value => [RequestMethod::OPTIONS],
        ];
    }
}
