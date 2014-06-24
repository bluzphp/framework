<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Application;

use Bluz\Http\Request;
use Bluz\Router\Router;
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
     * Tear Down
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->resetApp();
    }

    /**
     * @covers \Bluz\Application\Application::reflection
     * @return void
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
     *
     * @return void
     */
    public function testGettersOfPackages()
    {
        $this->assertInstanceOf('\Bluz\Acl\Acl', $this->getApp()->getAcl());
        $this->assertInstanceOf('\Bluz\Auth\Auth', $this->getApp()->getAuth());
        // cache disabled for testing
        $this->assertInstanceOf('\Bluz\Common\Nil', $this->getApp()->getCache());
        $this->assertInstanceOf('\Bluz\Config\Config', $this->getApp()->getConfig());
        $this->assertInstanceOf('\Bluz\Db\Db', $this->getApp()->getDb());
        $this->assertInstanceOf('\Bluz\EventManager\EventManager', $this->getApp()->getEventManager());
        $this->assertInstanceOf('\Bluz\View\Layout', $this->getApp()->getLayout());
        $this->assertInstanceOf('\Bluz\Logger\Logger', $this->getApp()->getLogger());
        $this->assertInstanceOf('\Bluz\Mailer\Mailer', $this->getApp()->getMailer());
        $this->assertInstanceOf('\Bluz\Messages\Messages', $this->getApp()->getMessages());
        $this->assertInstanceOf('\Bluz\Registry\Registry', $this->getApp()->getRegistry());
        $this->assertInstanceOf('\Bluz\Http\Request', $this->getApp()->getRequest());
        $this->assertInstanceOf('\Bluz\Http\Response', $this->getApp()->getResponse());
        $this->assertInstanceOf('\Bluz\Router\Router', $this->getApp()->getRouter());
        $this->assertInstanceOf('\Bluz\Session\Session', $this->getApp()->getSession());
        $this->assertInstanceOf('\Bluz\Translator\Translator', $this->getApp()->getTranslator());
        $this->assertInstanceOf('\Bluz\View\View', $this->getApp()->getView());
    }

    /**
     * @covers \Bluz\Application\Application::getConfigData
     * @return void
     */
    public function testGetConfigData()
    {
        // merged
        //  - configs/application.php
        //  - configs/app.testing.php
        // hardcoded numbers of configuration items
        $this->assertEquals(12, sizeof($this->getApp()->getConfigData()));
        $this->assertEquals(["foo" => "bar"], $this->getApp()->getConfigData("test"));
        $this->assertEquals("bar", $this->getApp()->getConfigData("test", "foo"));
    }

    /**
     * Test Registry configuration setup
     *
     * @return void
     */
    public function testRegistry()
    {
        $this->assertEquals(["moo" => "baz"], $this->getApp()->getConfigData("registry"));
        $this->assertEquals("baz", $this->getApp()->getConfigData("registry", "moo"));
        $this->assertEquals("baz", $this->getApp()->getRegistry()->moo);
    }

    /**
     * Test run Index Controller if Index Module
     */
    public function testIndexController()
    {
        // setup Request
        $request = $this->getApp()->getRequest();
        $request->setRequestUri('/');
        $request->setMethod(Request::METHOD_GET);
        $this->getApp()->setRequest($request);

        // run Router
        $this->getApp()->getRouter()->process();

        // run Application
        $this->getApp()->process();

        $this->assertEquals(Router::DEFAULT_MODULE, $this->getApp()->getModule());
        $this->assertEquals(Router::DEFAULT_CONTROLLER, $this->getApp()->getController());
    }

    /**
     * Test run Error Controller
     */
    public function testErrorController()
    {
        // setup Request
        $request = $this->getApp()->getRequest();
        $request->setRequestUri(uniqid('module'). '/'. uniqid('controller'));
        $request->setMethod(Request::METHOD_GET);
        $this->getApp()->setRequest($request);

        // run Router
        $this->getApp()->getRouter()->process();

        // run Application
        $this->getApp()->process();
        $this->assertEquals(Router::ERROR_MODULE, $this->getApp()->getModule());
        $this->assertEquals(Router::ERROR_CONTROLLER, $this->getApp()->getController());
    }
}
