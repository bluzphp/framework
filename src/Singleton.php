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
namespace Bluz;

/**
 * Singleton
 *
 * @category Bluz
 * @package  Trait
 *
 * @author   Anton Shevchuk
 * @created  16.05.12 14:26
 */
trait Singleton
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * Disabled by access level
     * @return self
     */
    protected function __construct()
    {
        static::setInstance($this);
    }

    /**
     * setInstance
     *
     * @param self $instance
     * @throws Exception
     * @return self
     */
    final static public function setInstance($instance)
    {
        if ($instance instanceof static) {
            static::$instance = $instance;
        } else {
            throw new Exception('First parameter for method `'.__METHOD__.'` should be instance of `'.__CLASS__.'`');
        }
        return static::$instance;
    }

    /**
     * getInstance
     *
     * @throws Exception
     * @return self
     */
    final static public function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    /**
     * Disabled by access level
     */
    protected function __wakeup() {

    }

    /**
     * Disabled by access level
     */
    protected function __clone() {

    }
}
