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
     * index
     *
     * @throws NotImplementedException
     * @return mixed
     */
    protected function index()
    {
        throw new NotImplementedException();
    }

    /**
     * read
     *
     * @throws NotImplementedException
     * @return mixed
     */
    protected function get()
    {
        throw new NotImplementedException();
    }

    /**
     * create
     *
     * @throws NotImplementedException
     * @return mixed
     */
    protected function post()
    {
        throw new NotImplementedException();
    }

    /**
     * update
     *
     * @throws NotImplementedException
     * @return mixed
     */
    protected function put()
    {
        throw new NotImplementedException();
    }

    /**
     * delete
     *
     * @throws NotImplementedException
     * @return mixed
     */
    protected function delete()
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     * @return mixed
     */
    public function __invoke()
    {
        $request = app()->getRequest();

        $params = $request->getParams();
        $allParams = $request->getAllParams();

        switch ($request->getMethod()) {
            case AbstractRequest::METHOD_GET:
                if (sizeof($params)) {
                    return $this->get($allParams);
                } else {
                    return $this->index($params);
                }
                break;
            case AbstractRequest::METHOD_POST:
                if (sizeof($params)) {

                }
                return $this->post($allParams);
                break;
            case AbstractRequest::METHOD_PUT:
                return $this->put($allParams);
                break;
            case AbstractRequest::METHOD_DELETE:
                return $this->delete($allParams);
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }
}
