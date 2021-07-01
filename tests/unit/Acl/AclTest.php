<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Acl;

use Bluz\Proxy;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Models\UserAdmin;
use Bluz\Tests\Fixtures\Models\UserGuest;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:09
 */
class AclTest extends FrameworkTestCase
{
    /**
     * Test allow access
     */
    public function testAllow()
    {
        Proxy\Auth::setIdentity(new UserAdmin());
        self::assertTrue(Proxy\Acl::isAllowed('any', 'any'));
    }

    /**
     * Test deny access
     */
    public function testDeny()
    {
        Proxy\Auth::setIdentity(new UserGuest());
        self::assertFalse(Proxy\Acl::isAllowed('any', 'any'));
    }
}
