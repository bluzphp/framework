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
use Bluz\Translator\Translator;

/**
 * Realization of Flash Messages
 *
 * @package  Bluz\Messages
 *
 * @author   Anton Shevchuk
 */
class Messages
{
    use Options;

    const TYPE_ERROR = 'error';
    const TYPE_SUCCESS = 'success';
    const TYPE_NOTICE = 'notice';

    /**
     * Stack of messages types
     * @var array
     */
    protected $types = array(
        self::TYPE_ERROR,
        self::TYPE_SUCCESS,
        self::TYPE_NOTICE
    );

    /**
     * get size of messages container
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
     * init
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
     * add notice
     *
     * @param string $text
     * @return Messages
     */
    public function addNotice($text)
    {
        $this->init();

        $this->getMessagesStore()[self::TYPE_NOTICE][] = Translator::translate($text);
        return $this;
    }

    /**
     * add success
     *
     * @param string $text
     * @return Messages
     */
    public function addSuccess($text)
    {
        $this->init();

        $this->getMessagesStore()[self::TYPE_SUCCESS][] = Translator::translate($text);
        return $this;
    }

    /**
     * add error
     *
     * @param string $text
     * @return Messages
     */
    public function addError($text)
    {
        $this->init();

        $this->getMessagesStore()[self::TYPE_ERROR][] = Translator::translate($text);
        return $this;
    }

    /**
     * Pop a message
     *
     * @param string $type
     * @return \stdClass
     */
    public function pop($type = null)
    {
        if (!$this->getMessagesStore()) {
            return null;
        }

        if ($type) {
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
     * @return \ArrayObject
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
     * Reset messages
     */
    public function reset()
    {
        app()->getSession()->MessagesStore = $this->createEmptyMessagesStore();
    }

    /**
     * Returns current messages store.
     *
     * @return \ArrayObject|null Returns null if store not exists yet
     */
    protected function getMessagesStore()
    {
        return app()->getSession()->MessagesStore;
    }

    /**
     * Creates a new empty store for messages.
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
