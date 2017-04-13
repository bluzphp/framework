<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Application;

use Bluz\Http\RequestMethod;
use Bluz\Http\StatusCode;
use Bluz\Proxy;
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
        self::setRequestParams('', [], [], RequestMethod::GET, ['Accept' => 'text/html']);

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
        self::setRequestParams(uniqid('module'). '/'. uniqid('controller'));

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
    }

    /**
     * Test call Error helper
     */
    public function testHelperError()
    {
        // setup Request
        self::setRequestParams('test/throw-exception');

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(Response::getStatusCode(), StatusCode::INTERNAL_SERVER_ERROR);
        self::assertEquals(Response::getBody()->getData()->get('code'), 500);
        self::assertEquals(Response::getBody()->getData()->get('message'), 'Message');
    }

    /**
     * Test call Forbidden helper
     */
    public function testHelperForbidden()
    {
        // setup Request
        self::setRequestParams('test/throw-forbidden');

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(Response::getStatusCode(), StatusCode::FORBIDDEN);
        self::assertEquals(Response::getBody()->getData()->get('code'), StatusCode::FORBIDDEN);
        self::assertEquals(Response::getBody()->getData()->get('message'), 'Forbidden');
    }

    /**
     * Test call Redirect helper
     */
    public function testHelperRedirect()
    {
        // setup Request
        self::setRequestParams('test/throw-redirect');

        // run Application
        self::getApp()->process();

        self::assertEquals(Response::getStatusCode(), StatusCode::FOUND);
        self::assertEquals(Response::getHeader('Location'), '/');
    }

    /**
     * Test call Redirect helper
     */
    public function testHelperRedirectAjaxCall()
    {
        // setup Request
        self::setRequestParams(
            'test/throw-redirect',
            [],
            [],
            RequestMethod::POST,
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        // run Application
        self::getApp()->process();

        self::assertEquals(Response::getStatusCode(), StatusCode::NO_CONTENT);
        self::assertEquals(Response::getHeader('Bluz-Redirect'), '/');
    }
}
