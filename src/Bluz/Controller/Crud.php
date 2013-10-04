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

use Bluz\Application\Exception\BadRequestException;
use Bluz\Application\Exception\NotFoundException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Crud\AbstractCrud;
use Bluz\Db\Table;
use Bluz\Request\AbstractRequest;

/**
 * Crud
 *
 * @category Bluz
 * @package  Controller
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 15:37
 */
class Crud extends AbstractController
{
    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        // switch by method
        // FIXME: hardcoded messages
        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                $result = $this->getCrud()->readOne($this->primary);

                // always HTML
//                app()->useJson(false);
//
//                // enable Layout for not AJAX request
//                if (!app()->getRequest()->isXmlHttpRequest()) {
//                    app()->useLayout(true);
//                }
                break;
            case AbstractRequest::METHOD_POST:
                $result = $this->getCrud()->createOne($this->data);
                app()->getMessages()->addSuccess("Row was created");
                break;
            case AbstractRequest::METHOD_PATCH:
            case AbstractRequest::METHOD_PUT:
                $result = $this->getCrud()->updateOne($this->primary, $this->data);
                app()->getMessages()->addSuccess("Row was updated");
                break;
            case AbstractRequest::METHOD_DELETE:
                $result = $this->getCrud()->deleteOne($this->primary);
                app()->getMessages()->addSuccess("Row was deleted");
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }
}
