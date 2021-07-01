<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Auth;

use Bluz\Proxy\Auth;
use Bluz\Proxy\Session;
use Bluz\Tests\Fixtures\Models\Auth\Table;
use Bluz\Tests\Fixtures\Models\UserAdmin;
use Bluz\Tests\FrameworkTestCase;
use Bluz\Auth\Model\AbstractRow;

/**
 * AuthTest
 *
 * @package  Bluz\Tests\Auth
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 11:12
 */
class AuthTest extends FrameworkTestCase
{
    /**
     * Test Auth works with Identity
     *
     * @covers \Bluz\Auth\Auth::setIdentity
     * @covers \Bluz\Auth\Auth::getIdentity
     */
    public function testAuthIdentity()
    {
        $adminIdentity = new UserAdmin();

        Auth::setIdentity($adminIdentity);

        self::assertEquals($adminIdentity, Auth::getIdentity());
    }

    /**
     * Test Auth Identity clear
     *
     * @covers \Bluz\Auth\Auth::clearIdentity
     */
    public function testAuthClearIdentity()
    {
        $adminIdentity = new UserAdmin();

        Auth::setIdentity($adminIdentity);
        Auth::clearIdentity();

        self::assertNull(Auth::getIdentity());
    }

    /**
     * Test Auth Identity clear
     *
     * @covers \Bluz\Auth\Auth::getIdentity
     * @covers \Bluz\Auth\Auth::clearIdentity
     */
    public function testAuthClearIdentityWithWrongUserAgent()
    {
        $adminIdentity = new UserAdmin();

        Session::set('auth:agent', 'agent:php');
        Session::set('auth:identity', $adminIdentity);

        $_SERVER['HTTP_USER_AGENT'] = 'agent:cli';

        self::assertNull(Auth::getIdentity());
    }

    /**
     * Test get Auth\Row
     */
    public function testGetAuthRow()
    {
        $authRow = Table::getInstance()::getAuthRow(Table::PROVIDER_EQUALS, 'admin');

        self::assertInstanceOf(AbstractRow::class, $authRow);
    }
}
