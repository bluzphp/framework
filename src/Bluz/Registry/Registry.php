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
namespace Bluz\Registry;

/**
 * Registry
 *
 * @category Bluz
 * @package  Registry
 *
 * @author   Anton Shevchuk
 */
class Registry
{
    use \Bluz\Package;

    /**
     * Stored data
     * 
     * @var array
     */
    protected $data = array();

    /**
     * reset data
     *
     * @param array $data
     * @return Registry
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return mixed
     */
     public function __isset($key)
     {
         return array_key_exists($key, $this->data);
     }

    /**
     * setter for key
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
     public function __set($key, $value)
     {
         $this->data[$key] = $value;
     }

    /**
     * getter for key
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }
}