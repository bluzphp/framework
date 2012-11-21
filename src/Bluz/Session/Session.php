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
namespace Bluz\Session;

/**
 * Session
 *
 * @category Bluz
 * @package  Session
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:19
 *
 * @property mixed MessagesStore
 * @property mixed identity
 */
class Session
{
    use \Bluz\Package;

    /**
     * Session store instance
     * @var Store\AbstractStore
     */
    protected $store = null;

    /**
     * Session store name
     * @var string
     */
    protected $storeName = 'session';

    /**
     * Session store options
     * @var string
     */
    protected $storeOptions = array();

    /**
     * setStore
     *
     * @param string $store description
     * @return Session
     */
    public function setStore($store)
    {
        $this->storeName = $store;
        return $this;
    }

    /**
     * setStore
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->storeOptions = $options;
    }

    /**
     * buildStore
     *
     * @return Session
     */
    public function start()
    {
        if (!$this->store) {
            // switch statement for $store
            switch ($this->storeName) {
                case 'array':
                    $this->store = new Store\ArrayStore($this->storeOptions);
                    break;
                case 'session':
                default:
                    $this->store = new Store\SessionStore($this->storeOptions);
                    break;
            }
        }
        return $this->store->start();
    }

    /**
     * getStore
     *
     * @throws SessionException
     * @return Store\AbstractStore
     */
    public function getStore()
    {
        if (!$this->store) {
            throw new SessionException("Session store is not configured");
        }

        return $this->store;
    }

    /**
     * __set
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function __set($key, $value)
    {
        return $this->getStore()->set($key, $value);
    }

    /**
     * __get
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getStore()->get($key);
    }
}
