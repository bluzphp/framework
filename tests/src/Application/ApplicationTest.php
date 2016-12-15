<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Application;

use Bluz\Proxy;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Tests\TestCase;
use Zend\Diactoros\ServerRequest;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ApplicationTest extends TestCase
{
    /**
     * Check all getters of Application
     */
    public function testGettersOfPackages()
    {
        // cache disabled for testing
        self::assertInstanceOf('\Zend\Diactoros\ServerRequest', self::getApp()->getRequest());
        self::assertInstanceOf('\Bluz\Response\Response', self::getApp()->getResponse());
    }

    /**
     * Test Application Config
     */
    public function testGetConfigData()
    {
        // merged
        //  - configs/default/
        //  - configs/testing/
        // hardcoded numbers of configuration items
        self::assertEquals(14, sizeof(Proxy\Config::getData()));
        self::assertEquals(["foo" => "bar"], Proxy\Config::getData("test"));
        self::assertEquals("bar", Proxy\Config::getData("test", "foo"));
    }

    /**
     * Test Registry configuration setup
     */
    public function testRegistry()
    {
        self::assertEquals(["moo" => "baz"], Proxy\Config::getData("registry"));
        self::assertEquals("baz", Proxy\Config::getData("registry", "moo"));
        self::assertEquals("baz", Proxy\Registry::get('moo'));
    }

    /**
     * Test run Index Controller if Index Module
     */
    public function testIndexController()
    {
        // setup Request
        $request = new ServerRequest([], [], '', Request::METHOD_GET, 'php://input', ['Accept' => 'text/html']);
        Request::setInstance($request);

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getDefaultModule(), self::getApp()->getModule());
        self::assertEquals(Router::getDefaultController(), self::getApp()->getController());
    }

    /**
     * Test run Error Controller
     */
    public function testErrorController()
    {
        // setup Request
        $request = new ServerRequest([], [], uniqid('module'). '/'. uniqid('controller'), Request::METHOD_GET);
        Request::setInstance($request);

        // run Application
        self::getApp()->process();
        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
    }
}
