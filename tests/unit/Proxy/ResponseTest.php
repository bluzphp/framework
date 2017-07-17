<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Tests\FrameworkTestCase;

/**
 * ResponseTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class ResponseTest extends FrameworkTestCase
{
    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        Response::resetInstance();
    }

    /**
     * Test Helper Redirect
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirect()
    {
        Response::redirect('/');
    }

    /**
     * Test Helper RedirectTo
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperRedirectTo()
    {
        Response::redirectTo(Router::getDefaultModule(), Router::getDefaultController());
    }

    /**
     * Test Helper Reload
     *
     * @expectedException \Bluz\Application\Exception\RedirectException
     */
    public function testHelperReload()
    {
        Response::reload();
    }
}
