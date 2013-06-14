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
namespace Bluz\Db\Query;

use Bluz\Db\Db;

/**
 * Class Expression
 * @package Bluz\Db\Query
 */
class CompositeBuilder implements \Countable
{
    /**
     * @var string type AND|OR
     */
    private $type;

    /**
     * @var array Parts of the composite expression
     */
    private $parts = array();

    /**
     * Constructor
     *
     * @param array $parts Parts of the composite expression
     * @param string $type AND|OR
     */
    public function __construct(array $parts = array(), $type = 'AND')
    {
        $this->type = (strtoupper($type)=='OR')?'OR':'AND';
        $this->add($parts);
    }

    /**
     * Adds an expression to composite expression.
     *
     * @param mixed $parts
     * @return CompositeBuilder
     */
    public function add($parts)
    {
        foreach ((array) $parts as $part) {
            if (!empty($part) || ($part instanceof self && $part->count() > 0)) {
                $this->parts[] = $part;
            }
        }

        return $this;
    }

    /**
     * Return type of this composite
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieves the amount of expressions on composite expression.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     * Retrieve the string representation of this composite expression.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->parts) === 1) {
            return (string) $this->parts[0];
        }
        return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
    }
}
