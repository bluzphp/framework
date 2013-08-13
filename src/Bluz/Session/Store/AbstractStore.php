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
namespace Bluz\Session\Store;

use Bluz\Common\Package;

/**
 * Abstract Session
 *
 * @category Bluz
 * @package  Session
 *
 * @author   Anton Shevchuk
 * @created  26.01.12 18:19
 */
abstract class AbstractStore
{
    use Package;

    /**
     * Session namespace
     *
     * @var string
     */
    protected $namespace = 'Bluz';

    /**
     * setNamespace
     *
     * @param string $namespace
     * @return AbstractStore
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * start
     *
     * @return bool
     */
    abstract public function start();

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    abstract public function __set($key, $value);

    /**
     * @param string $key
     * @return mixed|null
     */
    abstract public function __get($key);

    /**
     * @param string $key
     * @return boolean
     */
    abstract public function __isset($key);

    /**
     * @param string $key
     * @return void
     */
    abstract public function __unset($key);

    /**
     * destroy
     *
     * @return bool
     */
    abstract public function destroy();
}
