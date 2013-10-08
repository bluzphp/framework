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

use Bluz\Application\Exception\ApplicationException;
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
     * Identifier
     * @var string
     */
    protected $id;

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

        $params = $request->getRawParams();

        // %module% / %controller% / %id%
        if (sizeof($params)) {
            $this->id = array_shift($params);
        }
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
     * Get crud instance
     *
     * @throws \Bluz\Application\Exception\ApplicationException
     * @return AbstractCrud
     */
    public function getCrud()
    {
        if (!$this->crud) {
            $restClass = get_called_class();
            $crudClass = substr($restClass, 0, strrpos($restClass, '\\', 1) + 1) . 'Crud';

            // check class initialization
            if (!is_subclass_of($crudClass, '\Bluz\Crud\AbstractCrud')) {
                throw new ApplicationException("`Crud` class is not exists or not initialized");
            }

            /**
             * @var AbstractCrud $crudClass
             */
            $crud = $crudClass::getInstance();

            $this->setCrud($crud);
        }
        return $this->crud;
    }

    /**
     * Setup Crud instance
     *
     * @param AbstractCrud $crud
     * @return self
     */
    public function setCrud(AbstractCrud $crud)
    {
        $this->crud = $crud;
        return $this;
    }

    /**
     * Get item by primary key(s)
     *
     * @param mixed $primary
     * @return mixed
     */
    public function readOne($primary)
    {
        return $this->getCrud()->readOne($primary);
    }

    /**
     * List of items
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @return mixed
     */
    public function readSet($offset = 0, $limit = AbstractCrud::DEFAULT_LIMIT, array $params = array())
    {
        return $this->getCrud()->readSet($offset, $limit, $params);
    }

    /**
     * Create new item
     *
     * @param array $data
     * @return mixed
     */
    public function createOne(array $data)
    {
        return $this->getCrud()->createOne($data);
    }

    /**
     * Create items
     *
     * @param array $data
     * @return mixed
     */
    public function createSet(array $data)
    {
        return $this->getCrud()->createSet($data);
    }

    /**
     * Update item
     *
     * @param mixed $id
     * @param array $data
     * @return integer
     */
    public function updateOne($id, array $data)
    {
        return $this->getCrud()->updateOne($id, $data);
    }

    /**
     * Update items
     *
     * @param array $data
     * @return integer
     */
    public function updateSet(array $data)
    {
        return $this->getCrud()->updateSet($data);
    }

    /**
     * Delete item
     *
     * @param mixed $primary
     * @return integer
     */
    public function deleteOne($primary)
    {
        return $this->getCrud()->deleteOne($primary);
    }

    /**
     * Delete items
     *
     * @param array $data
     * @return integer
     */
    public function deleteSet(array $data)
    {
        return $this->getCrud()->deleteSet($data);
    }
}
