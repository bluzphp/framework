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
     * @covers \Bluz\Http\Request::__get()
     * @return void
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
     * @covers \Bluz\Http\Request::__get()
     * @return void
     */
    public function testGet()
    {
        $this->assertEquals('get', $this->getApp()->getRequest()->get);
        $this->assertEquals('post', $this->getApp()->getRequest()->post);
        $this->assertEquals('cookie', $this->getApp()->getRequest()->cookie);
        $this->assertEquals('server', $this->getApp()->getRequest()->server);
        $this->assertEquals('env', $this->getApp()->getRequest()->env);
        $this->assertNull($this->getApp()->getRequest()->null);
    }

    /**
     * Complex test for magic __isset
     *
     * @covers \Bluz\Http\Request::__isset()
     * @return void
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->getApp()->getRequest()->get));
        $this->assertTrue(isset($this->getApp()->getRequest()->post));
        $this->assertTrue(isset($this->getApp()->getRequest()->cookie));
        $this->assertTrue(isset($this->getApp()->getRequest()->server));
        $this->assertTrue(isset($this->getApp()->getRequest()->env));
        $this->assertFalse(isset($this->getApp()->getRequest()->null));
    }

    /**
     * Complex test for magic __unset
     *
     * @covers \Bluz\Http\Request::__unset()
     * @return void
     */
    public function testUnset()
    {
        unset(
            $this->getApp()->getRequest()->get,
            $this->getApp()->getRequest()->post,
            $this->getApp()->getRequest()->cookie,
            $this->getApp()->getRequest()->server,
            $this->getApp()->getRequest()->env
        );

        $this->assertFalse(isset($this->getApp()->getRequest()->get));
        $this->assertFalse(isset($this->getApp()->getRequest()->post));
        $this->assertFalse(isset($this->getApp()->getRequest()->cookie));
        $this->assertFalse(isset($this->getApp()->getRequest()->server));
        $this->assertFalse(isset($this->getApp()->getRequest()->env));
    }
}
