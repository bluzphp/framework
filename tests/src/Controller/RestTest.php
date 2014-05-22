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
class RestTest extends TestCase
{
    /**
     * GET with PRIMARY should return RECORD
     */
    public function testGetRecord()
    {
        $request = $this->getApp()->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setRawParams([1]);

        $restController = new Controller\Rest();
        $restController->setCrud(TestCrud::getInstance());
        $result = $restController();

        $row = current($result);

        $this->assertInstanceOf('Bluz\Tests\Fixtures\Models\TestRow', $row);
        $this->assertEquals(1, $row['id']);
    }
}
