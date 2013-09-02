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
namespace Bluz\Cache\Adapter;

use Bluz\Cache\InvalidArgumentException;
use Bluz\Cache\CacheInterface;

/**
 * Base class for all cache adapters within Bluz\Cache package
 * @author murzik
 */
abstract class AbstractAdapter implements CacheInterface
{
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * Setup adapter settings
     */
    public function __construct($settings = array())
    {
        $this->settings = array_replace_recursive($this->settings, $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $id = $this->castToString($id);
        return $this->doGet($id);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        $id = $this->castToString($id);
        return $this->doContains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function add($id, $data, $ttl = 0)
    {
        $id = $this->castToString($id);
        return $this->doAdd($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $ttl = 0)
    {
        $id = $this->castToString($id);
        return $this->doSet($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = $this->castToString($id);
        return $this->doDelete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->doFlush();
    }

    /**
     * Cast given $inputValue to string.
     * @param mixed $inputValue
     * @throws InvalidArgumentException if given $input value not a number or string
     * @return string $castedToString
     * @internal defence from "fool".
     *           Attempt to cast to string object will lead to cache entry with id "Object".
     *           Which is wrong.
     */
    protected function castToString($inputValue)
    {
        if (!is_string($inputValue) && !is_int($inputValue)) {
            $msg = "<String> or <Integer> expected. But "
                . "<" . gettype($inputValue) . "> given.";
            throw new InvalidArgumentException($msg);
        }

        return (string)$inputValue;
    }

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::flush() goes here
     * @see \Bluz\Cache\CacheInterface::flush()
     */
    abstract protected function doFlush();

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::get() goes here
     * @see \Bluz\Cache\CacheInterface::get()
     */
    abstract protected function doGet($id);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::add() goes here
     * @see \Bluz\Cache\CacheInterface::add()
     */
    abstract protected function doAdd($id, $data, $ttl = 0);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::set() goes here
     * @see \Bluz\Cache\CacheInterface::set()
     */
    abstract protected function doSet($id, $data, $ttl = 0);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::delete() goes here
     * @see \Bluz\Cache\CacheInterface::delete()
     */
    abstract protected function doDelete($id);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::contains() goes here
     * @see Bluz\Cache\CacheInterface::contains()
     */
    abstract protected function doContains($id);
}
