<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Acl;

use Bluz\Proxy;
use Bluz\Tests\Fixtures\Models\UserAdmin;
use Bluz\Tests\Fixtures\Models\UserGuest;
use Bluz\Tests\Unit\Unit;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests\Unit
 */
class AclTest extends Unit
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
