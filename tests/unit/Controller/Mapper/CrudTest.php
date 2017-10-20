<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
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
use Bluz\Tests\FrameworkTestCase;

/**
 * Test for Controller Crud Mapper
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class CrudTest extends FrameworkTestCase
{
    /**
     * @var Crud
     */
    protected $mapper;

    public function setUp()
    {
        parent::setUp();
        $crudTable = TableCrud::getInstance();
        $crudTable->setTable(Table::getInstance());
        $this->mapper = new Crud($crudTable);
    }

    public function tearDown()
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
     * @param string $method
     */
    public function testMethods($method)
    {
        self::setRequestParams('test/index', [], [], $method);
        $this->mapper->addMap($method, 'test', 'index');
        $controller = $this->mapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    /**
     * @dataProvider dataMethods
     *
     * @param string $method
     */
    public function testMethodsAliases($method)
    {
        self::setRequestParams('test/index', [], [], $method);

        $alias = strtolower($method);

        $this->mapper->$alias('test', 'index');

        $controller = $this->mapper->run();

        self::assertInstanceOf(Controller::class, $controller);
    }

    /**
     * @expectedException \Bluz\Application\Exception\ForbiddenException
     */
    public function testForbiddenMethod()
    {
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
            RequestMethod::HEAD => [RequestMethod::HEAD],
            RequestMethod::GET => [RequestMethod::GET],
            RequestMethod::POST => [RequestMethod::POST],
            RequestMethod::PATCH => [RequestMethod::PATCH],
            RequestMethod::PUT => [RequestMethod::PUT],
            RequestMethod::DELETE => [RequestMethod::DELETE],
            RequestMethod::OPTIONS => [RequestMethod::OPTIONS],
        ];
    }
}
