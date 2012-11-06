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
namespace Bluz\Session\Store;

/**
 * Stub
 *
 * @category Bluz
 * @package  Session
 *
 * @author   Anton Shevchuk
 * @created  26.01.12 13:18
 */
class ArrayStore extends AbstractStore
{
    /**
     * @var array
     */
    protected $store = array();

    /**
     * start
     *
     * @return bool
     */
    public function start()
    {
        $this->store[$this->namespace] = array();
        return true;
    }

    /**
     * __set
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function set($key, $value)
    {
        return $this->store[$this->namespace][$key] = $value;
    }

    /**
     * __get
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->store[$this->namespace][$key])?$this->store[$this->namespace][$key]:null;
    }

    /**
     * destroy
     *
     * @return bool
     */
    public function destroy()
    {
        $this->store[$this->namespace] = array();
        return true;
    }
}
