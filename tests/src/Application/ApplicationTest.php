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
        $this->assertInstanceOf('\Zend\Diactoros\ServerRequest', $this->getApp()->getRequest());
        $this->assertInstanceOf('\Bluz\Response\Response', $this->getApp()->getResponse());
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
        $this->assertEquals(14, sizeof(Proxy\Config::getData()));
        $this->assertEquals(["foo" => "bar"], Proxy\Config::getData("test"));
        $this->assertEquals("bar", Proxy\Config::getData("test", "foo"));
    }

    /**
     * Test Registry configuration setup
     */
    public function testRegistry()
    {
        $this->assertEquals(["moo" => "baz"], Proxy\Config::getData("registry"));
        $this->assertEquals("baz", Proxy\Config::getData("registry", "moo"));
        $this->assertEquals("baz", Proxy\Registry::get('moo'));
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
        $this->getApp()->process();

        $this->assertEquals(Router::getDefaultModule(), $this->getApp()->getModule());
        $this->assertEquals(Router::getDefaultController(), $this->getApp()->getController());
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
        $this->getApp()->process();
        $this->assertEquals(Router::getErrorModule(), $this->getApp()->getModule());
        $this->assertEquals(Router::getErrorController(), $this->getApp()->getController());
    }

}
