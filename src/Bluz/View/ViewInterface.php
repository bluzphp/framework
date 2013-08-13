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
namespace Bluz\View;

use Bluz\Application\Application;

/**
 * ViewInterface
 *
 * @category Bluz
 * @package  View
 *
 * @author   Anton Shevchuk
 * @created  19.02.13 15:25
 */
interface ViewInterface
{
    /**
     * setup path to templates
     *
     * <code>
     * $view->setPath('/modules/users/views');
     * </code>
     *
     * @param string $path
     * @return ViewInterface
     */
    public function setPath($path);

    /**
     * setup template
     *
     * <code>
     * $view->setTemplate('index.phtml');
     * </code>
     *
     * @param string $file
     * @return ViewInterface
     */
    public function setTemplate($file);

    /**
     * merge data from array
     *
     * @param array $data
     * @return ViewInterface
     */
    public function setData($data = array());

    /**
     * get data as array
     *
     * @return array
     */
    public function getData();
}
