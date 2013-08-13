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
namespace Bluz\Common;

/**
 * Package
 *
 * @category Bluz
 * @package  Package
 *
 * <pre>
 * <code>
 * class Foo
 * {
 *   use \Bluz\Package;
 *
 *   protected $bar = '';
 *   protected $baz = '';
 *
 *   public function setBar($value)
 *   {
 *       $this->bar = $value;
 *   }
 *
 *   public function setBaz($value)
 *   {
 *       $this->baz = $value;
 *   }
 * }
 *
 * $Foo = new Foo(array('bar'=>123, 'baz'=>456));
 * </code>
 * </pre>
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 16:15
 */
trait Package
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        // store options by default
        $this->options = $options;

        // apply options
        foreach ($options as $key => $value) {
            $method = 'set' . $this->normalizeKey($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        // check options
        $this->checkOptions();
        // initialization
        $this->initOptions();
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Validation
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        return true;
    }

    /**
     * Initialization
     *
     * @throws \Bluz\Config\ConfigException
     * @return void
     */
    protected function initOptions()
    {
        return;
    }

    /**
     * get option by key
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return false;
        }
    }

    /**
     * @param  $key
     * @return mixed
     */
    private function normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
