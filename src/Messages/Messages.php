<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Messages;

use ArrayObject;
use Bluz\Common\Options;
use Bluz\Proxy\Session;
use Bluz\Proxy\Translator;
use stdClass;

/**
 * Realization of Flash Messages
 *
 * @package  Bluz\Messages
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Messages
 */
class Messages
{
    use Options;

    private const TYPE_ERROR = 'error';
    private const TYPE_SUCCESS = 'success';
    private const TYPE_NOTICE = 'notice';

    /**
     * @var array list of messages types
     */
    protected $types = [
        self::TYPE_ERROR,
        self::TYPE_SUCCESS,
        self::TYPE_NOTICE
    ];

    /**
     * Add notice
     *
     * @param  string   $message
     * @param  string[] $text
     *
     * @return void
     * @since  1.0.0 added $text
     */
    public function addNotice($message, ...$text): void
    {
        $this->add(self::TYPE_NOTICE, $message, ...$text);
    }

    /**
     * Add success
     *
     * @param  string   $message
     * @param  string[] $text
     *
     * @return void
     * @since  1.0.0 added $text
     */
    public function addSuccess($message, ...$text): void
    {
        $this->add(self::TYPE_SUCCESS, $message, ...$text);
    }

    /**
     * Add error
     *
     * @param  string   $message
     * @param  string[] $text
     *
     * @return void
     * @since  1.0.0 added $text
     */
    public function addError($message, ...$text): void
    {
        $this->add(self::TYPE_ERROR, $message, ...$text);
    }

    /**
     * Add message to container
     *
     * @param  string   $type One of error, notice or success
     * @param  string   $message
     * @param  string[] $text
     *
     * @return void
     */
    protected function add($type, $message, ...$text): void
    {
        $this->getMessagesStore()[$type][] = Translator::translate($message, ...$text);
    }

    /**
     * Pop a message by type
     *
     * @param  string $type
     *
     * @return stdClass|null
     */
    public function pop($type): ?stdClass
    {
        $text = array_shift($this->getMessagesStore()[$type]);
        if ($text) {
            $message = new stdClass();
            $message->text = $text;
            $message->type = $type;
            return $message;
        }
        return null;
    }

    /**
     * Pop all messages
     *
     * @return ArrayObject|array
     */
    public function popAll()
    {
        $messages = $this->getMessagesStore()->getArrayCopy();
        $this->resetMessagesStore();
        return $messages;
    }

    /**
     * Get size of messages container
     *
     * @return integer
     */
    public function count(): int
    {
        $size = 0;
        if (!$store = $this->getMessagesStore()) {
            return $size;
        }
        foreach ($store as $messages) {
            $size += count($messages);
        }
        return $size;
    }

    /**
     * Reset messages
     *
     * @param ArrayObject $store
     * @return void
     */
    protected function setMessagesStore(ArrayObject $store): void
    {
        Session::set('messages:store', $store);
    }

    /**
     * Returns current or new messages store if it not exists
     *
     * @return ArrayObject
     */
    protected function getMessagesStore(): ArrayObject
    {
        if (!$store = Session::get('messages:store')) {
            $this->resetMessagesStore();
        }
        return Session::get('messages:store');
    }

    /**
     * Reset messages
     *
     * @return void
     */
    protected function resetMessagesStore(): void
    {
        $this->setMessagesStore($this->createEmptyMessagesStore());
    }

    /**
     * Creates a new empty store for messages
     *
     * @return ArrayObject
     */
    protected function createEmptyMessagesStore(): ArrayObject
    {
        return new ArrayObject(
            [
                self::TYPE_ERROR => [],
                self::TYPE_SUCCESS => [],
                self::TYPE_NOTICE => []
            ]
        );
    }
}
