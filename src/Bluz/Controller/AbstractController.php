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
namespace Bluz\Controller;

use Bluz\Crud\AbstractCrud;
use Bluz\Request\AbstractRequest;

/**
 * AbstractController
 *
 * @category Bluz
 * @package  Controller
 *
 * @author   Anton Shevchuk
 * @created  02.10.13 13:52
 */
abstract class AbstractController
{
    /**
     * HTTP Method
     * @var string
     */
    protected $method = AbstractRequest::METHOD_GET;

    /**
     * Params of query
     * @var array
     */
    protected $params = array();

    /**
     * Query data
     * @var array
     */
    protected $data = array();

    /**
     * @var AbstractCrud
     */
    protected $crud;

    /**
     * Prepare request for processing
     */
    public function __construct()
    {
        $request = app()->getRequest();

        // rewrite REST with "_method" param
        // this is workaround
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        // get all params
        $query = $request->getQuery();

        unset($query['_method']);

        $this->params = $query;

        $this->data = $request->getParams();

    }

    /**
     * @return mixed
     */
    abstract public function __invoke();

    /**
     * Return HTTP request method
     * Can be rewrite by '_method' parameter
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * getData
     *
     * @param string $field
     * @return array
     */
    public function getData($field = null)
    {
        if (null !== $field) {
            if (isset($this->data[$field])) {
                return $this->data[$field];
            } else {
                return null;
            }
        }

        return $this->data;
    }

    /**
     * get crud instance
     *
     * @return AbstractCrud
     */
    public function getCrud()
    {
        if (!$this->crud) {
            $restClass = get_called_class();
            $crudClass = substr($restClass, 0, strrpos($restClass, '\\', 1) + 1) . 'Crud';

            /**
             * @var AbstractCrud
             */
            $crud = $crudClass::getInstance();

            $this->setCrud($crud);
        }
        return $this->crud;
    }

    /**
     * setCrud
     *
     * @param AbstractCrud $crud
     * @return self
     */
    public function setCrud($crud)
    {
        $this->crud = $crud;
        return $this;
    }
}
