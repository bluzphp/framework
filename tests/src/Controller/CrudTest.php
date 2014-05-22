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
     * GET with PRIMARY should return RECORD
     */
    public function testGetRecord()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setParam('id', 1);

        $crudController = new Controller\Crud();
        $crudController->setCrud(TestCrud::getInstance());
        $result = $crudController();

        $this->assertEquals(Request::METHOD_PUT, $result['method']);
        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $result['row']);
        $this->assertEquals(1, $result['row']['id']);
    }
}
