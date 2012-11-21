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
namespace Bluz\Db;

use Bluz\Db\DbException;

/**
 * Rowset
 *
 * @category Bluz
 * @package  Db
 *
 * @author   Anton Shevchuk
 * @created  07.07.11 15:36
 */
class Rowset implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * Iterator pointer.
     *
     * @var integer
     */
    protected $pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $count = 0;

    /**
     * How many data rows in Db w/out LIMIT.
     *
     * @var integer
     */
    protected $total;

    /**
     * Collection of instantiated Bluz\Db\Row objects.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (isset($data['total'])) {
            $this->total = $data['total'];
        }
        if (isset($data['data'])) {
            $this->data = $data['data'];
        }
        $this->count = sizeof($this->data);
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return \Bluz\Db\Rowset|void Fluent interface.
     */
    public function rewind()
    {
        $this->pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return \Bluz\Db\Row|mixed current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }
        // return the row object
        return $this->data[$this->pointer];
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int|\scalar
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->pointer >= 0 && $this->pointer < $this->count;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return Rowset
     * @throws \OutOfBoundsException
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position >= $this->count) {
            throw new \OutOfBoundsException("Illegal index $position");
        }
        $this->pointer = $position;
        return $this;
    }


    /**
     * Check if an offset exists
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[(int) $offset]);
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @throws \OutOfBoundsException
     * @return \Bluz\Db\Row|mixed
     */
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= $this->count) {
            throw new \OutOfBoundsException("Illegal index $offset");
        }
        $this->pointer = $offset;

        return $this->current();
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }
}
