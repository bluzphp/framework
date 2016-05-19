<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Controller;

use Bluz\Controller;
use Bluz\Proxy\Router;
use Bluz\Tests\TestCase;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  19.05.16 12:28
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller\Controller
     */
    protected $controller;
    
    /**
     * Create `index/index` controller
     */
    public function setUp()
    {
        $this->controller = $this->getApp()->dispatch('index', 'index');
    }
    
    /**
     * Close all
     */
    public function tearDown()
    {
        
    }

    /**
     * Test Helper Denied
     *
     * @expectedException \Bluz\Application\Exception\ForbiddenException
     */
    public function testHelperDenied()
    {
        $this->controller->denied();
    }

    /**
     * Test Helper Redirect
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirect()
    {
        $this->controller->redirect('/');
    }

    /**
     * Test Helper RedirectTo
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirectTo()
    {
        $this->controller->redirectTo(Router::getDefaultModule(), Router::getDefaultController());
    }

    /**
     * Test Helper Reload
     *
     * @expectedException \Bluz\Application\Exception\ReloadException
     */
    public function testHelperReload()
    {
        $this->controller->reload();
    }

    /**
     * Test Helper User
     */
    public function testHelperUser()
    {
        $result = $this->controller->user();
        $this->assertNull($result);
    }
}
