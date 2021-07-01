<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Controller\Mapper;

use Bluz\Controller\Controller;
use Bluz\Controller\Mapper\Rest;
use Bluz\Http\Exception\NotImplementedException;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\RequestMethod;
use Bluz\Tests\Fixtures\Crud\TableCrud;
use Bluz\Tests\Fixtures\Models\Test\Table;
use Bluz\Tests\FrameworkTestCase;

/**
 * Test for Controller Rest Mapper
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class RestTest extends FrameworkTestCase
{
    /**
     * @var Rest
     */
    protected $mapper;

    public function setUp()
    {
        parent::setUp();
        $crudTable = TableCrud::getInstance();
        $crudTable->setTable(Table::getInstance());
        $this->mapper = new Rest($crudTable);
    }

    public function tearDown()
    {
        parent::tearDown();
        TableCrud::getInstance()->resetTable();
    }

    public function testCheckGetRequestWithPrimary()
    {
        self::setRequestParams('test/mapper/42', [], [], RequestMethod::GET);

        $this->mapper->addMap(RequestMethod::GET, 'test', 'mapper');

        $controller = $this->mapper->run();

        $data = $controller->getData();

        self::assertArrayHasKey('primary', $data);
        self::assertArrayHasKey('id', $data['primary']);
        self::assertEquals(42, $data['primary']['id']);
    }

    public function testCheckGetRequestWithPrimaryAndRelation()
    {
        self::setRequestParams('test/mapper/42/relation/6', [], [], RequestMethod::GET);

        $this->mapper->addMap(RequestMethod::GET, 'test', 'mapper');

        $controller = $this->mapper->run();

        $data = $controller->getData();

        self::assertEquals('relation', $data['relation']);
        self::assertEquals(6, $data['relationId']);
    }

    /**
     * @dataProvider dataMethods
     *
     * @param string $method
     */
    public function testMethod($method)
    {
        self::setRequestParams('test/index', [], [], $method);
        $this->mapper->addMap($method, 'test', 'index');
        $controller = $this->mapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    public function testNotImplementedMethod()
    {
        $this->expectException(NotImplementedException::class);
        $this->mapper->run();
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
            RequestMethod::GET => [RequestMethod::GET],
            RequestMethod::POST => [RequestMethod::POST],
            RequestMethod::PATCH => [RequestMethod::PATCH],
            RequestMethod::PUT => [RequestMethod::PUT],
            RequestMethod::DELETE => [RequestMethod::DELETE],
        ];
    }
}
