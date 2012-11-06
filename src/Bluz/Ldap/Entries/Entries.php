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
namespace Bluz\Ldap\Entries;

use ArrayAccess;
use IteratorAggregate;

/**
 * Ldap search results entries
 *
 * @category Bluz
 * @package Ldap
 */
class Entries implements ArrayAccess, IteratorAggregate
{

    /**
     * Entries
     *
     * @var array
     */
    private $entries;
    /**
     * Count
     *
     * @var int
     */
    public $count = 0;

    /**
     * Construct
     *
     * @param array $ldapData
     */
    public function __construct($ldapData = null)
    {
        if ($ldapData) {
            $this->loadEntries($ldapData);
        }
    }

    /**
     * Exists
     *
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (isset($this->entries[$offset])) {
            return TRUE;
        }
        return false;
    }

    /**
     * Get
     *
     * @param int $offset
     * @return \Bluz\Ldap\Entries\Entry|mixed
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->entries[$offset];
        }
        return false;
    }

    /**
     * Set
     *
     * @param int $offset
     * @param Entry $value
     */
    public function offsetSet($offset, $value)
    {
        if ($offset) {
            $this->entries[$offset] = $value;
        } else {
            $this->entries[] = $value;
        }
    }

    /**
     * Unset
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->entries[$offset]);
    }

    /**
     * Iterator
     *
     * @return \ArrayIterator|\Traversable
     */
    public function &getIterator()
    {
        return new \ArrayIterator($this->entries);
    }

    /**
     * Load data
     *
     * @param array $ldapData
     */
    public function loadEntries($ldapData)
    {
        if ($ldapData["count"] <= 0) {
            $this->count = 0;
            return;
        } else {
            $this->count = $ldapData["count"];
        }

        for ($i = 0; $i < $ldapData["count"]; $i++) {
            $this->entries[] = new Entry($ldapData[$i]);
        }
    }

}