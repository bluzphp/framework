<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Messages;

/**
 * Realization of Flash Messages
 *
 * @category Bluz
 * @package  Messages
 *
 * @author   Anton Shevchuk
 */
class Messages
{
    use \Bluz\Package;

    const TYPE_ERROR   = 'error';
    const TYPE_SUCCESS = 'success';
    const TYPE_NOTICE  = 'notice';

    /**
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
        $this->init();

        $size = 0;
        foreach ($this->getApplication()->getSession()->MessagesStore as $messages) {
            $size += sizeof($messages);
        }
        return $size;
    }

    /**
     * init
     *
     * @return Messages
     */
    public function init()
    {
        if (!$this->getApplication()->getSession()->MessagesStore) {
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

        $this->getApplication()->getSession()->MessagesStore[self::TYPE_NOTICE][] = $text;
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

        $this->getApplication()->getSession()->MessagesStore[self::TYPE_SUCCESS][] = $text;
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

        $this->getApplication()->getSession()->MessagesStore[self::TYPE_ERROR][] = $text;
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
        $this->init();

        if ($type) {
            $text = array_shift($this->getApplication()->getSession()->MessagesStore[$type]);
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
        $this->init();

        $messages = $this->getApplication()->getSession()->MessagesStore->getArrayCopy();
        $this->reset();
        return $messages;
    }

    /**
     * Reset messages
     *
     * @return Messages
     */
    public function reset()
    {
        $this->getApplication()->getSession()->MessagesStore = new \ArrayObject(array(
            self::TYPE_ERROR   => array(),
            self::TYPE_SUCCESS => array(),
            self::TYPE_NOTICE  => array()
        ));
    }
}