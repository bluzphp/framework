<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Proxy;

use Bluz\Http\Exception\RedirectException;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Tests\Unit\Unit;

/**
 * ResponseTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class ResponseTest extends Unit
{
    /**
     * Test Helper Redirect
     */
    public function testHelperRedirect()
    {
        $this->expectException(RedirectException::class);
        Response::redirect('/');
    }

    /**
     * Test Helper RedirectTo
     */
    public function testHelperRedirectTo()
    {
        $this->expectException(RedirectException::class);
        Response::redirectTo(Router::getDefaultModule(), Router::getDefaultController());
    }

    /**
     * Test Helper Reload
     */
    public function testHelperReload()
    {
        $this->expectException(RedirectException::class);
        Response::reload();
    }
}
