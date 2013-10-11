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
use Bluz\Crud\ValidationException;
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
     * Prepare request for processing
     */
    public function __construct()
    {
        parent::__construct();

        // %module% / %controller% / %id% / %relation% / %id%
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $this->id = $this->data['id'];
        }
    }
    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        // switch by method
        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                $row = $this->readOne($this->id);

                $result = ['row' => $row];
                if ($this->id) {
                    // update form
                    $result['method'] = AbstractRequest::METHOD_PUT;
                } else {
                    // create form
                    $result['method'] = AbstractRequest::METHOD_POST;
                }
                return $result;
                break;
            case AbstractRequest::METHOD_POST:
                try {
                    $this->createOne($this->data);
                } catch (ValidationException $e) {
                    $row = $this->readOne(null);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $this->getCrud()->getErrors(),
                        'method' => $this->getMethod()
                    ];
                    return $result;
                }
                break;
            case AbstractRequest::METHOD_PATCH:
            case AbstractRequest::METHOD_PUT:
                try {
                    $this->updateOne($this->id, $this->data);
                } catch (ValidationException $e) {
                    $row = $this->readOne($this->id);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $this->getCrud()->getErrors(),
                        'method' => $this->getMethod()
                    ];
                    return $result;
                }
                break;
            case AbstractRequest::METHOD_DELETE:
                $this->deleteOne($this->id);
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createOne($data)
    {
        $result = $this->getCrud()->createOne($data);

        app()->getMessages()->addSuccess("Record was created");

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOne($id, $data)
    {
        $result = $this->getCrud()->updateOne($id, $data);

        app()->getMessages()->addSuccess("Record was updated");

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($primary)
    {
        $result = $this->getCrud()->deleteOne($primary);

        app()->getMessages()->addSuccess("Record was deleted");

        return $result;
    }
}
