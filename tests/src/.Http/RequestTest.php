<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Http;

use Bluz\Proxy\Request;
use Bluz\Tests\TestCase;

/**
 * RequestTest
 *
 * @package  Bluz\Tests\Http
 *
 * @author   Anton Shevchuk
 * @created  21.08.2014 10:08
 */
class RequestTest extends TestCase
{
    /**
     * Setup environment
     */
    public static function setUpBeforeClass()
    {
        $_GET['get'] = 'get';
        $_POST['post'] = 'post';
        $_COOKIE['cookie'] = 'cookie';
        $_SERVER['server'] = 'server';
        $_ENV['env'] = 'env';

        $_SERVER['REQUEST_METHOD'] = 'GET';

        self::resetApp();
    }

    /**
     * Reset Application
     */
    public static function tearDownAfterClass()
    {
        unset($_GET['get'], $_POST['post'], $_COOKIE['cookie'], $_SERVER['server'], $_ENV['env']);

        self::resetApp();
    }

    /**
     * Test `Is` Methods
     */
    public function testIsMethods()
    {
        $this->assertTrue(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertFalse(Request::isPut());
        $this->assertFalse(Request::isDelete());
        $this->assertFalse(Request::isFlashRequest());
        $this->assertFalse(Request::isXmlHttpRequest());
    }

    /**
     * Complex test of getters
     *
     * @covers \Bluz\Http\Request::getQuery()
     * @covers \Bluz\Http\Request::getPost()
     * @covers \Bluz\Http\Request::getCookie()
     * @covers \Bluz\Http\Request::getServer()
     * @covers \Bluz\Http\Request::getEnv()
     */
    public function testGetters()
    {
        $this->assertEquals('get', Request::getQuery('get'));
        $this->assertEquals($_GET, Request::getQuery());
        $this->assertEquals('post', Request::getPost('post'));
        $this->assertEquals($_POST, Request::getPost());
        $this->assertEquals('cookie', Request::getCookie('cookie'));
        $this->assertEquals($_COOKIE, Request::getCookie());
        $this->assertEquals('server', Request::getServer('server'));
        $this->assertEquals($_SERVER, Request::getServer());
        $this->assertEquals('env', Request::getEnv('env'));
        $this->assertEquals($_ENV, Request::getEnv());
    }

    /**
     * Complex test for magic getter
     *
     * @covers \Bluz\Http\Request::getParam()
     */
    public function testGet()
    {
        $this->assertEquals('get', Request::getParam('get'));
        $this->assertEquals('post', Request::getParam('post'));
        $this->assertEquals('cookie', Request::getParam('cookie'));
        $this->assertEquals('server', Request::getParam('server'));
        $this->assertEquals('env', Request::getParam('env'));
        $this->assertNull(Request::getParam('some'));
    }
}
