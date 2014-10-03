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

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ApplicationTest extends TestCase
{
    /**
     * @covers \Bluz\Application\Application::reflection
     */
    public function testReflection()
    {
        $controllerFile = dirname(__FILE__) .'/../Fixtures/Controllers/ConcreteWithData.php';

        $reflectionData = $this->getApp()->reflection($controllerFile);

        /** @var \closure $controllerClosure */
        $controllerClosure = require $controllerFile;

        $this->assertEquals($reflectionData, $controllerClosure('a', 'b', 'c'));
    }


    /**
     * Check all getters of Application
     */
    public function testGettersOfPackages()
    {
        // cache disabled for testing
        $this->assertInstanceOf('\Bluz\Db\Db', $this->getApp()->getDb());
        $this->assertInstanceOf('\Bluz\Layout\Layout', $this->getApp()->getLayout());
        $this->assertInstanceOf('\Bluz\Http\Request', $this->getApp()->getRequest());
        $this->assertInstanceOf('\Bluz\Http\Response', $this->getApp()->getResponse());
        $this->assertInstanceOf('\Bluz\Router\Router', $this->getApp()->getRouter());
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
        $this->assertEquals(13, sizeof(Proxy\Config::getData()));
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
        Request::setRequestUri('/');
        Request::setMethod(Request::METHOD_GET);


        // run Router
        Router::process();

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
        Request::setRequestUri(uniqid('module'). '/'. uniqid('controller'));
        Request::setMethod(Request::METHOD_GET);

        // run Router
        Router::process();

        // run Application
        $this->getApp()->process();
        $this->assertEquals(Router::getErrorModule(), $this->getApp()->getModule());
        $this->assertEquals(Router::getErrorController(), $this->getApp()->getController());
    }

    /**
     * Test Response Json
     */
    public function testRender()
    {
        $this->expectOutputString('foo');
        Response::setBody('foo');
        $this->getApp()->render();
    }

    /**
     * Test Helper Denied
     *
     * @expectedException \Bluz\Application\Exception\ForbiddenException
     */
    public function testHelperDenied()
    {
        $this->getApp()->denied();
    }

    /**
     * Test Helper Redirect
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirect()
    {
        $this->getApp()->redirect('/');
    }

    /**
     * Test Helper RedirectTo
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirectTo()
    {
        $this->getApp()->redirectTo(Router::getDefaultModule(), Router::getDefaultController());
    }

    /**
     * Test Helper Reload
     *
     * @expectedException \Bluz\Application\Exception\ReloadException
     */
    public function testHelperReload()
    {
        $this->getApp()->reload();
    }

    /**
     * Test Helper User
     */
    public function testHelperUser()
    {
        $result = $this->getApp()->user();
        $this->assertNull($result);
    }
}
