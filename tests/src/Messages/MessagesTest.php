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
     * Test Messages container
     */
    public function testMessages()
    {
        $this->getApp()->getMessages()->addError('error');
        $this->getApp()->getMessages()->addNotice('notice');
        $this->getApp()->getMessages()->addSuccess('success');

        $this->assertEquals(3, $this->getApp()->getMessages()->count());

        $this->assertInstanceOf('\stdClass', $this->getApp()->getMessages()->pop(Messages::TYPE_ERROR));
        $this->assertInstanceOf('\stdClass', $this->getApp()->getMessages()->pop(Messages::TYPE_NOTICE));
        $this->assertInstanceOf('\stdClass', $this->getApp()->getMessages()->pop(Messages::TYPE_SUCCESS));
    }

    /**
     * Test Messages container
     */
    public function testMessagesPop()
    {
        $this->getApp()->getMessages()->addError('error');
        $this->getApp()->getMessages()->addNotice('notice');
        $this->getApp()->getMessages()->addSuccess('success');

        $counter = 0;
        while ($this->getApp()->getMessages()->pop()) {
            $counter++;
        }

        $this->assertEquals(3, $counter);
    }

    /**
     * Test Messages container
     */
    public function testMessagesPopAll()
    {
        $this->getApp()->getMessages()->addError('error');
        $this->getApp()->getMessages()->addNotice('notice');
        $this->getApp()->getMessages()->addSuccess('success');

        $messages = $this->getApp()->getMessages()->popAll();

        $this->assertArrayHasKeyAndSize($messages, 'error', 1);
        $this->assertArrayHasKeyAndSize($messages, 'notice', 1);
        $this->assertArrayHasKeyAndSize($messages, 'success', 1);
    }

    /**
     * Test Messages container
     */
    public function testMessagesPopAllForEmpty()
    {
        $messages = $this->getApp()->getMessages()->popAll();

        $this->assertArrayHasKey('error', $messages);
        $this->assertArrayHasKey('notice', $messages);
        $this->assertArrayHasKey('success', $messages);
    }
}
 