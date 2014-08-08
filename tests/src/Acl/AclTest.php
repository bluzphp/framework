<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Acl;

use Bluz\Tests\TestCase;
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
class AclTest extends TestCase
{
    /**
     * Test allow access
     */
    public function testAllow()
    {
        $this->getApp()->getAuth()->setIdentity(new UserAdmin());
        $this->assertTrue($this->getApp()->getAcl()->isAllowed('any', 'any'));
    }
    /**
     * Test deny access
     */
    public function testDeny()
    {
        $this->getApp()->getAuth()->setIdentity(new UserGuest());
        $this->assertFalse($this->getApp()->getAcl()->isAllowed('any', 'any'));
    }
}
