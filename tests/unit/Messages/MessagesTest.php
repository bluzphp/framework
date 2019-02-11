<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Messages;

use Bluz\Messages\Messages;
use Bluz\Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * MessagesTest
 *
 * @package  Bluz\Tests\Messages
 *
 * @author   Anton Shevchuk
 * @created  08.08.2014 14:23
 */
class MessagesTest extends FrameworkTestCase
{
    /**
     * setUp
     *
     * @return void
     * @throws \Bluz\Application\Exception\ApplicationException
     */
    public function setUp()
    {
        // initialize application
        self::getApp();
    }

    /**
     * Test Messages container
     */
    public function testMessages()
    {
        Proxy\Messages::addError('error');
        Proxy\Messages::addNotice('notice');
        Proxy\Messages::addSuccess('success');

        self::assertEquals(3, Proxy\Messages::count());

        self::assertInstanceOf(\stdClass::class, Proxy\Messages::pop('error'));
        self::assertInstanceOf(\stdClass::class, Proxy\Messages::pop('notice'));
        self::assertInstanceOf(\stdClass::class, Proxy\Messages::pop('success'));
    }

    /**
     * Test Messages container
     */
    public function testMessagesWithDirectives()
    {
        Proxy\Messages::addError('error %d %d %d', 1, 2, 3);
        Proxy\Messages::addNotice('notice %1$s %2$s %1$s', 'a', 'b');
        Proxy\Messages::addSuccess('success %01.2f', 1.020304);

        $error = Proxy\Messages::pop('error');
        self::assertEquals('error 1 2 3', $error->text);

        $notice = Proxy\Messages::pop('notice');
        self::assertEquals('notice a b a', $notice->text);

        $success = Proxy\Messages::pop('success');
        self::assertEquals('success 1.02', $success->text);
    }

    /**
     * Test Messages with empty container
     */
    public function testMessagesEmpty()
    {
        self::assertEquals(0, Proxy\Messages::count());
        self::assertNull(Proxy\Messages::pop('error'));
        self::assertNull(Proxy\Messages::pop('notice'));
        self::assertNull(Proxy\Messages::pop('success'));
    }

    /**
     * Test Messages container
     */
    public function testMessagesPop()
    {
        Proxy\Messages::addError('error');
        Proxy\Messages::addNotice('notice');
        Proxy\Messages::addSuccess('success');

        self::assertNotEmpty(Proxy\Messages::pop('error'));
        self::assertNotEmpty(Proxy\Messages::pop('notice'));
        self::assertNotEmpty(Proxy\Messages::pop('success'));
    }

    /**
     * Test Messages container
     */
    public function testMessagesPopAll()
    {
        Proxy\Messages::addError('error');
        Proxy\Messages::addNotice('notice');
        Proxy\Messages::addSuccess('success');

        $messages = Proxy\Messages::popAll();

        self::assertArrayHasKeyAndSize($messages, 'error', 1);
        self::assertArrayHasKeyAndSize($messages, 'notice', 1);
        self::assertArrayHasKeyAndSize($messages, 'success', 1);
    }

    /**
     * Test Messages container
     */
    public function testMessagesPopAllForEmpty()
    {
        $messages = Proxy\Messages::popAll();

        self::assertArrayHasKey('error', $messages);
        self::assertArrayHasKey('notice', $messages);
        self::assertArrayHasKey('success', $messages);
    }
}
