<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Router;

use Bluz\Proxy\Router;
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
     * @dataProvider providerForDefaultRoutes
     * @param string $url
     * @param string $module
     * @param string $controller
     * @param array $params
     */
    public function testRouterUrl($url, $module, $controller, $params = array())
    {
        $this->assertEquals($url, Router::getUrl($module, $controller, $params));
    }

    /**
     * Test Router Url for custom controller route
     * 
     * @dataProvider providerForCustomRoutes
     * @param string $url
     * @param string $module
     * @param string $controller
     * @param array $params
     */
    public function testRouterUrlWithCustomControllerRoute($url, $module, $controller, $params = array())
    {
        $this->assertEquals($url, Router::getUrl($module, $controller, $params));
    }

    /**
     * Test Router Url
     */
    public function testRouterFullUrl()
    {
        if (!isset($_SERVER['SERVER_NAME'])) {
            $_SERVER['SERVER_NAME'] = 'localhost';
        }

        $this->assertEquals('http://'.$_SERVER['SERVER_NAME'].'/', Router::getFullUrl());
    }

    /**
     * @return array
     */
    public function providerForDefaultRoutes()
    {
        return array(
            ['/test/test', 'test', 'test', array()],
            ['/test/test/foo/bar', 'test', 'test', ['foo'=>'bar']],
            ['/test/test?foo%5B0%5D=bar&foo%5B1%5D=baz', 'test', 'test', ['foo'=> ['bar', 'baz']]],
            ['/test', 'test', null, array()],
            ['/test', 'test', 'index', array()],
            ['/test/index/foo/bar', 'test', 'index', ['foo'=>'bar']],
            ['/index/test', null, 'test', array()],
            ['/index/test', 'index', 'test', array()],
            ['/index/test/foo/bar', 'index', 'test', ['foo'=>'bar']],
        );
    }
    
    /**
     * @return array
     */
    public function providerForCustomRoutes()
    {
        return array(
            ['/another-route.html', 'test', 'route-static'],
            ['/another-route.html?foo%5B0%5D=bar&foo%5B1%5D=baz', 'test', 'route-static', ['foo'=> ['bar', 'baz']]],
            ['/test/param/42/', 'test', 'route-with-param', ['a' => 42]],
            ['/foo-bar-baz/', 'test', 'route-with-params', ['a' => 'foo', 'b' => 'bar', 'c' => 'baz']],
            ['/test/route-with-other-params/about', 'test', 'route-with-other-params', ['alias' => 'about']],
        );
    }
}
