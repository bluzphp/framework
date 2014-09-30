<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Http;

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
        $this->assertTrue($this->getApp()->getRequest()->isGet());
        $this->assertFalse($this->getApp()->getRequest()->isPost());
        $this->assertFalse($this->getApp()->getRequest()->isPut());
        $this->assertFalse($this->getApp()->getRequest()->isDelete());
        $this->assertFalse($this->getApp()->getRequest()->isFlashRequest());
        $this->assertFalse($this->getApp()->getRequest()->isXmlHttpRequest());
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
        $this->assertEquals('get', $this->getApp()->getRequest()->getQuery('get'));
        $this->assertEquals($_GET, $this->getApp()->getRequest()->getQuery());
        $this->assertEquals('post', $this->getApp()->getRequest()->getPost('post'));
        $this->assertEquals($_POST, $this->getApp()->getRequest()->getPost());
        $this->assertEquals('cookie', $this->getApp()->getRequest()->getCookie('cookie'));
        $this->assertEquals($_COOKIE, $this->getApp()->getRequest()->getCookie());
        $this->assertEquals('server', $this->getApp()->getRequest()->getServer('server'));
        $this->assertEquals($_SERVER, $this->getApp()->getRequest()->getServer());
        $this->assertEquals('env', $this->getApp()->getRequest()->getEnv('env'));
        $this->assertEquals($_ENV, $this->getApp()->getRequest()->getEnv());
    }

    /**
     * Complex test for magic getter
     *
     * @covers \Bluz\Http\Request::getParam()
     */
    public function testGet()
    {
        $this->assertEquals('get', $this->getApp()->getRequest()->getParam('get'));
        $this->assertEquals('post', $this->getApp()->getRequest()->getParam('post'));
        $this->assertEquals('cookie', $this->getApp()->getRequest()->getParam('cookie'));
        $this->assertEquals('server', $this->getApp()->getRequest()->getParam('server'));
        $this->assertEquals('env', $this->getApp()->getRequest()->getParam('env'));
        $this->assertNull($this->getApp()->getRequest()->getParam('some'));
    }
}
