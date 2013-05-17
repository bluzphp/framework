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
namespace Bluz\Filter;

use Bluz\Exception;

/**
 * Sanitize
 *
 * TODO: UNDER CONSTRUCTION!!!
 *
 * <code>
 * $bar = new Bar();
 * $bar -> text = '<strong>Hello!</strong>';
 *
 * $foo = new Sanitize($bar);
 * echo $foo->text; // &lt;strong&gt;Hello!&lt;/strong&gt;
 * </code>
 *
 * @category Bluz
 * @package  Filter
 *
 * @author   Anton Shevchuk
 * @created  18.03.13 11:25
 */
class Sanitize
{
    const FILTER_HTML = 1;    // 1 << 0 ==   0001
    const FILTER_QUOTES = 2;  // 1 << 1 ==   0010
    const FILTER_HTML_QUOTES = 3;
                              // 1 << 2 ==   0100
    const FILTER_METHODS = 8; // 1 << 3 ==   1000
                              // 1 << 4 == 1 0000
    /**
     * FILTER_HTML | FILTER_QUOTES | FILTER_METHODS
     */
    const FILTER_ALL = 31;    // 0001 1111

    protected $rawSource;
    protected $flags;

    /**
     * Constructor of Sanitize
     *
     * @access  public
     */
    public function __construct($source, $flags = self::FILTER_HTML_QUOTES)
    {
        if (!is_array($source) && !is_object($source)) {
            throw new Exception(
                "Sanitize calls works only with objects and arrays. <br/>\n".
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Filter'>https://github.com/bluzphp/framework/wiki/Filter</a>"
            );
        }

        $this->rawSource = $source;
        $this->flags = $flags;
    }

    /**
     * __get
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (is_array($this->rawSource)) {
            if (!isset($this->rawSource[$key])) {
                return null;
            }
            $value = $this->rawSource[$key];
        } else {
            if (!isset($this->rawSource->$key)) {
                return null;
            }
            $value = $this->rawSource->$key;
        }

        return $this->filter($value);
    }

    /**
     * __call
     *
     * @param $method
     * @param $arguments
     * @throws \Bluz\Exception
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!is_object($this->rawSource)) {
            throw new Exception("You can't call method of array");
        }
        $value = call_user_func_array([$this->rawSource, $method], $arguments);
        if ($this->flags & self::FILTER_METHODS) {
            return $this->filter($value);
        } else {
            return $value;
        }
    }

    /**
     * filter
     *
     * @param $value
     * @return mixed
     */
    protected function filter($value)
    {
        if ($this->flags & self::FILTER_HTML) {
            if ($this->flags & self::FILTER_QUOTES) {
                return htmlentities($value, ENT_QUOTES | ENT_IGNORE);
            } else {
                return htmlentities($value);
            }
        } else {
            return filter_var($value, FILTER_SANITIZE_STRING);
        }
    }
}
