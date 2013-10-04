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
namespace Bluz\Rest;

use Bluz\Application\Exception\BadRequestException;
use Bluz\Application\Exception\NotFoundException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Request\AbstractRequest;

/**
 * Rest
 *
 * @category Bluz
 * @package  Rest
 *
 * @author   Anton Shevchuk
 * @created  13.08.13 13:09
 */
abstract class AbstractRest
{
    /**
     * HTTP Method
     * @var string
     */
    protected $method = AbstractRequest::METHOD_GET;

    /**
     * Identifier
     * @var string
     */
    protected $id;

    /**
     * Relation list
     * @var string
     */
    protected $relation;

    /**
     * Relation Id
     * @var string
     */
    protected $relationId;

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
     * Prepare request for processing
     */
    public function __construct()
    {
        $request = app()->getRequest();

        // rewrite REST with "_method" param
        // this is workaround
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        $params = $request->getRawParams();

        // %module% / %controller% / %id% / %relation% / %id%
        if (sizeof($params)) {
            $this->id = array_shift($params);
        }
        if (sizeof($params)) {
            $this->relation = array_shift($params);
        }
        if (sizeof($params)) {
            $this->relationId = array_shift($params);
        }

        // get all params
        $query = $request->getQuery();

        unset($query['_method']);

        $this->params = $query;

        $this->data = $request->getParams();
    }

    /**
     * getMethod
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get item
     *
     * @param $id
     * @throws NotImplementedException
     * @return mixed
     */
    protected function readOne($id)
    {
        throw new NotImplementedException();
    }

    /**
     * list of items
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @throws \Bluz\Application\Exception\NotImplementedException
     * @return mixed
     */
    protected function readSet($offset = 0, $limit = 10, array $params = array())
    {
        throw new NotImplementedException();
    }

    /**
     * create new item
     *
     * @param array $data
     * @throws NotImplementedException
     * @return mixed
     */
    protected function createOne(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * create items
     *
     * @param array $data
     * @throws NotImplementedException
     * @return mixed
     */
    protected function createSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * update item
     *
     * @param $id
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    protected function updateOne($id, array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * update items
     *
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    protected function updateSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * delete item
     *
     * @param $id
     * @throws NotImplementedException
     * @return integer
     */
    protected function deleteOne($id)
    {
        throw new NotImplementedException();
    }

    /**
     * delete items
     *
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    protected function deleteSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        $request = app()->getRequest();

        $accept = $request->getHeader('accept');
        $accept = explode(',', $accept);
        if (in_array("application/json", $accept)) {
            app()->useJson(true);
        }

        // everyone method can return:
        // >> 401 Unauthorized - if authorization is required
        // >> 403 Forbidden - if user don't have permissions
        // >> 501 Not Implemented - if something not exists

        // GET    /module/rest/   -> 200 // return collection or
        //                        -> 206 // return part of collection
        // GET    /module/rest/id -> 200 // return one item or
        //                        -> 404 // not found
        // POST   /module/rest/   -> 201 // item created or
        //                        -> 400 // bad request, validation error
        // POST   /module/rest/id -> 501 // error, not used in REST
        // PATCH  /module/rest/
        // PUT    /module/rest/   -> 200 // all items was updated or
        //                        -> 207 // multi-status ?
        // PATCH  /module/rest/id
        // PUT    /module/rest/id -> 200 // item was updated or
        //                        -> 304 // item not modified or
        //                        -> 400 // bad request, validation error or
        //                        -> 404 // not found
        // DELETE /module/rest/   -> 204 // all items was deleted or
        //                        -> 207 // multi-status ?
        // DELETE /module/rest/id -> 204 // item was deleted
        //                        -> 404 // not found

        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                if ($this->id) {
                    $result = $this->readOne($this->id);
                    if (!sizeof($result)) {
                        throw new NotFoundException();
                    }
                    return current($result);
                } else {
                    // setup default offset and limit - safe way
                    $offset = isset($this->params['offset'])?$this->params['offset']:0;
                    $limit = isset($this->params['limit'])?$this->params['limit']:10;

                    // get offset and limit from request headers
                    if ($range = $request->getHeader('Range')) {
                        list(, $offset, $last) = preg_split('/[-=]/', $range);
                        // for better compatibility
                        $limit = $last - $offset;
                    }

                    return $this->readSet($offset, $limit, $this->params);
                }
                break;
            case AbstractRequest::METHOD_POST:
                if ($this->id) {
                    // can't create entity with predefined ID
                    throw new BadRequestException();
                }
                if (!sizeof($this->data)) {
                    // can't create empty entity
                    throw new BadRequestException();
                }

                $result = $this->createOne($this->data);
                if (!$result) {
                    throw new NotFoundException();
                }
                http_response_code(201);
                header(
                    'Location: '.app()->getRouter()->url($request->getModule(), $request->getController()).'/'.$result
                );
                return false; // disable view
                break;
            case AbstractRequest::METHOD_PATCH:
            case AbstractRequest::METHOD_PUT:
                if (!sizeof($this->data)) {
                    // data not found
                    throw new BadRequestException();
                }
                if ($this->id) {
                    // update one item
                    $result = $this->updateOne($this->id, $this->data);
                } else {
                    // update collection
                    $result = $this->updateSet($this->data);
                }
                // if $result === 0 it's means a update is not apply
                // or records not found
                if (0 === $result) {
                    http_response_code(304);
                }
                return false; // disable view
                break;
            case AbstractRequest::METHOD_DELETE:
                if ($this->id) {
                    // delete one
                    $result = $this->deleteOne($this->id);
                } else {
                    // delete collection
                    if (!sizeof($this->data)) {
                        // data not found
                        throw new BadRequestException();
                    }
                    $result = $this->deleteSet($this->data);
                }
                // if $result === 0 it's means a delete is not apply
                // or records not found
                if (0 === $result) {
                    throw new NotFoundException();
                } else {
                    http_response_code(204);
                }
                return false; // disable view
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }
}
