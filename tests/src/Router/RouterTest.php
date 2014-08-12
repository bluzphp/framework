<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Router;

use Bluz\Router\Router;
use Bluz\Tests\TestCase;

/**
 * RouterTest
 *
 * @package  Bluz\Tests\Router
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 14:24
 */
class RouterTest extends TestCase
{

    /**
     * testRouterUrl
     *
     * @dataProvider providerForEquals
     * @param string $url
     * @param string $module
     * @param string $controller
     * @param array $params
     */
    public function testRouterUrl($url, $module, $controller, $params)
    {
        $this->assertEquals($url, $this->getApp()->getRouter()->url($module, $controller, $params));
    }

    /**
     * Test Router Url for custom controller route
     */
    public function testRouterUrlWithCustomControllerRoute()
    {
        $router = $this->getApp()->getRouter();

        $this->assertEquals(
            '/another-route.html',
            $router->url('test', 'route-static')
        );
        $this->assertEquals(
            '/test/param/42/',
            $router->url('test', 'route-with-param', ['a' => 42])
        );
        $this->assertEquals(
            '/foo-bar-baz/',
            $router->url('test', 'route-with-params', ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'])
        );
        $this->assertEquals(
            '/test/route-with-other-params/about',
            $router->url('test', 'route-with-other-params', ['alias' => 'about'])
        );
    }

    /**
     * Test Router Url
     */
    public function testRouterBaseUrl()
    {
        $router = $this->getApp()->getRouter();

        $this->assertEquals('/', $router->getBaseUrl());
    }

    /**
     * Test Router Url
     */
    public function testRouterFullUrl()
    {
        $router = $this->getApp()->getRouter();

        if (!isset($_SERVER['SERVER_NAME'])) {
            $_SERVER['SERVER_NAME'] = 'localhost';
        }

        $this->assertEquals('http://'.$_SERVER['SERVER_NAME'].'/', $router->getFullUrl());
    }

    /**
     * @return array
     */
    public function providerForEquals()
    {
        return array(
            array('/test/test', 'test', 'test', array()),
            array('/test/test/foo/bar', 'test', 'test', ['foo'=>'bar']),
            array('/test', 'test', Router::DEFAULT_CONTROLLER, array()),
            array('/test/'.Router::DEFAULT_CONTROLLER.'/foo/bar', 'test', Router::DEFAULT_CONTROLLER, ['foo'=>'bar']),
            array('/'.Router::DEFAULT_MODULE.'/test', Router::DEFAULT_MODULE, 'test', array()),
            array('/'.Router::DEFAULT_MODULE.'/test/foo/bar', Router::DEFAULT_MODULE, 'test', ['foo'=>'bar']),
        );
    }
}
