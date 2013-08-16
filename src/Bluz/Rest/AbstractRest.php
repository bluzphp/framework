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

use Bluz\Application\Exception\NotFoundException;
use Bluz\Request\AbstractRequest;
use Bluz\Application\Exception\NotImplementedException;

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
     * getMethod
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * list of items
     *
     * @param array $params
     * @throws NotImplementedException
     * @return mixed
     */
    protected function index(array $params = array())
    {
        throw new NotImplementedException();
    }

    /**
     * read item
     *
     * @param $id
     * @throws NotImplementedException
     * @return mixed
     */
    protected function get($id)
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
    protected function post(array $data)
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
    protected function put($id, array $data)
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
    protected function delete($id)
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @return mixed
     */
    public function __invoke()
    {
//        app()->useJson(true);
        $request = app()->getRequest();

        // rewrite REST with "method" param
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        // try to get uid
        $uri = parse_url($request->getRequestUri(), PHP_URL_PATH);
        $url = app()->getRouter()->url(null, null);
        $id = trim(substr($uri, strlen($url)), '/');

        // get all params
        $allParams = $request->getAllParams();

        unset($allParams['_method']);

        // GET    /module/rest/   -> collection
        // GET    /module/rest/id -> get item
        // POST   /module/rest/   -> create item
        // PUT    /module/rest/id -> update item
        // DELETE /module/rest/id -> delete item
        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                if ($id) {
                    $result = $this->get($id);
                    if (!sizeof($result)) {
                        throw new NotFoundException();
                    }
                    return current($result);
                } else {
                    return $this->index($allParams);
                }
                break;
            case AbstractRequest::METHOD_POST:
                if ($id) {
                    throw new NotFoundException();
                }
                $result = $this->post($allParams);
                header(
                    'Location: '.app()->getRouter()->url($request->getModule(), $request->getController()).'/'.$result,
                    true,
                    201
                );
                return false;
                break;
            case AbstractRequest::METHOD_PUT:
                if (!$id) {
                    throw new NotFoundException();
                }
                return $this->put($id, $allParams);
                break;
            case AbstractRequest::METHOD_DELETE:
                if (!$id) {
                    throw new NotFoundException();
                }
                $result = $this->delete($id);
                if (!$result) {
                    throw new NotFoundException();
                }
                return false;
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }
}
