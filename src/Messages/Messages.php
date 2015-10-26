<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Messages;

use Bluz\Common\Options;
use Bluz\Proxy\Session;
use Bluz\Proxy\Translator;

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

    const TYPE_ERROR = 'error';
    const TYPE_SUCCESS = 'success';
    const TYPE_NOTICE = 'notice';

    /**
     * @var array list of messages types
     */
    protected $types = array(
        self::TYPE_ERROR,
        self::TYPE_SUCCESS,
        self::TYPE_NOTICE
    );

    /**
     * Initialize Messages container
     *
     * @return Messages
     */
    protected function init()
    {
        if (!$this->getMessagesStore()) {
            $this->reset();
        }
        return $this;
    }

    /**
     * Add notice
     *
     * @param  string   $message
     * @param  string[] $text
     * @return void
     * @since  1.0.0 added $text
     */
    public function addNotice($message, ...$text)
    {
        $this->add(self::TYPE_NOTICE, $message, ...$text);
    }

    /**
     * Add success
     *
     * @param  string   $message
     * @param  string[] $text
     * @return void
     * @since  1.0.0 added $text
     */
    public function addSuccess($message, ...$text)
    {
        $this->add(self::TYPE_SUCCESS, $message, ...$text);
    }

    /**
     * Add error
     *
     * @param  string   $message
     * @param  string[] $text
     * @return void
     * @since  1.0.0 added $text
     */
    public function addError($message, ...$text)
    {
        $this->add(self::TYPE_ERROR, $message, ...$text);
    }

    /**
     * Add message to container
     *
     * @param  string   $type One of error, notice or success
     * @param  string   $message
     * @param  string[] $text
     * @return void
     */
    protected function add($type, $message, ...$text)
    {
        $this->init();
        $this->getMessagesStore()[$type][] = Translator::translate($message, ...$text);
    }

    /**
     * Pop a message
     *
     * @param  string $type
     * @return \stdClass|null
     */
    public function pop($type = null)
    {
        if (!$this->getMessagesStore()) {
            return null;
        }

        if ($type !== null) {
            $text = array_shift($this->getMessagesStore()[$type]);
            if ($text) {
                $message = new \stdClass();
                $message->text = $text;
                $message->type = $type;
                return $message;
            }
        } else {
            foreach ($this->types as $type) {
                if ($message = $this->pop($type)) {
                    return $message;
                }
            }
        }
        return null;
    }

    /**
     * Pop all messages
     *
     * @return array
     */
    public function popAll()
    {
        if (!$this->getMessagesStore()) {
            return $this->createEmptyMessagesStore();
        }

        $messages = $this->getMessagesStore()->getArrayCopy();
        $this->reset();
        return $messages;
    }

    /**
     * Get size of messages container
     *
     * @return integer
     */
    public function count()
    {
        $size = 0;
        if (!$store = $this->getMessagesStore()) {
            return $size;
        }
        foreach ($store as $messages) {
            $size += sizeof($messages);
        }
        return $size;
    }

    /**
     * Reset messages
     *
     * @return void
     */
    public function reset()
    {
        Session::set('messages:store', $this->createEmptyMessagesStore());
    }

    /**
     * Returns current messages store
     *
     * @return \ArrayObject|null Returns null if store not exists yet
     */
    protected function getMessagesStore()
    {
        return Session::get('messages:store');
    }

    /**
     * Creates a new empty store for messages
     *
     * @return \ArrayObject
     */
    protected function createEmptyMessagesStore()
    {
        return new \ArrayObject(
            array(
                self::TYPE_ERROR => array(),
                self::TYPE_SUCCESS => array(),
                self::TYPE_NOTICE => array()
            )
        );
    }
}
