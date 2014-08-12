<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Auth;

use Bluz\Tests\TestCase;
use Bluz\Tests\Fixtures\Models\UserAdmin;

/**
 * AuthTest
 *
 * @package  Bluz\Tests\Auth
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 11:12
 */
class AuthTest extends TestCase
{
    /**
     * Test Auth works with Identity
     *
     * @covers \Bluz\Auth\Auth::setIdentity
     * @covers \Bluz\Auth\Auth::getIdentity
     */
    public function testAuthIdentity()
    {
        $auth = $this->getApp()->getAuth();

        $adminIdentity = new UserAdmin();

        $auth->setIdentity($adminIdentity);

        $this->assertEquals($adminIdentity, $auth->getIdentity());
    }

    /**
     * Test Auth Identity clear
     *
     * @covers \Bluz\Auth\Auth::setIdentity
     * @covers \Bluz\Auth\Auth::getIdentity
     */
    public function testAuthClearIdentity()
    {
        $auth = $this->getApp()->getAuth();

        $adminIdentity = new UserAdmin();

        $auth->setIdentity($adminIdentity);
        $auth->clearIdentity();

        $this->assertNull($auth->getIdentity());
    }
}
