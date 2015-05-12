<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Messages;

use Bluz\Messages\Messages;
use Bluz\Proxy;
use Bluz\Tests\TestCase;

/**
 * MessagesTest
 *
 * @package  Bluz\Tests\Messages
 *
 * @author   Anton Shevchuk
 * @created  08.08.2014 14:23
 */
class MessagesTest extends TestCase
{
    /**
     * setUp
     *
     * @return void
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

        $this->assertEquals(3, Proxy\Messages::count());

        $this->assertInstanceOf('\stdClass', Proxy\Messages::pop(Messages::TYPE_ERROR));
        $this->assertInstanceOf('\stdClass', Proxy\Messages::pop(Messages::TYPE_NOTICE));
        $this->assertInstanceOf('\stdClass', Proxy\Messages::pop(Messages::TYPE_SUCCESS));
    }

    /**
     * Test Messages container
     */
    public function testMessagesWithDirectives()
    {
        Proxy\Messages::addError('error %d %d %d', 1, 2, 3);
        Proxy\Messages::addNotice('notice %1$s %2$s %1$s', 'a', 'b');
        Proxy\Messages::addSuccess('success %01.2f', 1.020304);

        $error = Proxy\Messages::pop(Messages::TYPE_ERROR);
        $this->assertEquals('error 1 2 3', $error->text);

        $notice = Proxy\Messages::pop(Messages::TYPE_NOTICE);
        $this->assertEquals('notice a b a', $notice->text);

        $success = Proxy\Messages::pop(Messages::TYPE_SUCCESS);
        $this->assertEquals('success 1.02', $success->text);

    }

    /**
     * Test Messages with empty container
     */
    public function testMessagesEmpty()
    {
        $this->assertEquals(0, Proxy\Messages::count());
        $this->assertNull(Proxy\Messages::pop(Messages::TYPE_ERROR));
        $this->assertNull(Proxy\Messages::pop(Messages::TYPE_NOTICE));
        $this->assertNull(Proxy\Messages::pop(Messages::TYPE_SUCCESS));
    }

    /**
     * Test Messages container
     */
    public function testMessagesPop()
    {
        Proxy\Messages::addError('error');
        Proxy\Messages::addNotice('notice');
        Proxy\Messages::addSuccess('success');

        $counter = 0;
        while (Proxy\Messages::pop()) {
            $counter++;
        }

        $this->assertEquals(3, $counter);
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

        $this->assertArrayHasKeyAndSize($messages, 'error', 1);
        $this->assertArrayHasKeyAndSize($messages, 'notice', 1);
        $this->assertArrayHasKeyAndSize($messages, 'success', 1);
    }

    /**
     * Test Messages container
     */
    public function testMessagesPopAllForEmpty()
    {
        $messages = Proxy\Messages::popAll();

        $this->assertArrayHasKey('error', $messages);
        $this->assertArrayHasKey('notice', $messages);
        $this->assertArrayHasKey('success', $messages);
    }
}
