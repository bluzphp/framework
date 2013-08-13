<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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

use Bluz\Common\Package;

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
    use Package;

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
     * Session store parameters
     * @var string
     */
    protected $storeSettings = array();

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
     * setSettings
     *
     * @param array $settings
     * @return Session
     */
    public function setSettings(array $settings)
    {
        $this->storeSettings = $settings;
        return $this;
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
            // switch statement for $store
            switch ($this->storeName) {
                case 'array':
                    $this->store = new Store\ArrayStore();
                    break;
                case 'session':
                default:
                    $this->store = new Store\SessionStore();
                    break;
            }
            $this->store->setOptions($this->storeSettings);
        }

        return $this->store;
    }

    /**
     * __set
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->getStore()->__set($key, $value);
    }

    /**
     * __get
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getStore()->__get($key);
    }

    /**
     * Isset Offset
     *
     * @param  mixed $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->getStore()->__isset($key);
    }

    /**
     * Unset Offset
     *
     * @param  mixed $key
     * @return void
     */
    public function __unset($key)
    {
        $this->getStore()->__unset($key);
    }
}
