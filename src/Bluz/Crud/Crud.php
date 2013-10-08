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
namespace Bluz\Crud;

use Bluz\Application\Exception\ReloadException;
use Bluz\Common\Package;
use Bluz\Db;
use Bluz\Request\AbstractRequest;

/**
 * Crud
 *
 * @category Bluz
 * @package  Crud
 *
 * @deprecated since 1.7
 *
 * @author   AntonShevchuk
 * @created  15.08.12 15:37
 */
class Crud
{
    use Package;

    /**
     * @var string
     */
    protected $method = AbstractRequest::METHOD_GET;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var \Bluz\Db\Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Result of last CRUD operation (one of get/put/post/delete)
     * @var mixed
     */
    protected $result;

    /**
     * process CRUD from controller
     *
     * @throws CrudException
     * @return mixed
     */
    public function processController()
    {
        try {
            $result = $this->getResult();

            // switch statement for $this->getMethod()
            if ($this->getMethod() == AbstractRequest::METHOD_GET) {
                // EDIT or CREATE form
                if ($result instanceof Db\Row) {
                    // edit form
                    return [
                        'row' => $result,
                        'method' => AbstractRequest::METHOD_PUT
                    ];
                } else {
                    // create form
                    return [
                        'row' => $this->getTable()->create(),
                        'method' => AbstractRequest::METHOD_POST
                    ];
                }
            }
        } catch (CrudException $e) {
            // all "not found" errors and other similar
            app()->getMessages()->addError($e->getMessage());
            return false;
        } catch (ValidationException $e) {
            // validate errors
            app()->getMessages()->addError("Please fix all errors");

            $return = [
                'errors' => $this->getErrors(),
                'method' => $this->getMethod()
            ];

            switch ($this->getMethod()) {
                case AbstractRequest::METHOD_PATCH:
                case AbstractRequest::METHOD_POST:
                case AbstractRequest::METHOD_PUT:
                    $return['row'] = $this->getTable()->create($this->data);
                    break;
            }

            return $return;
        }

        // disable view
        return false;
    }

    /**
     * @throws CrudException
     * @throws ValidationException
     * @return mixed
     */
    public function processRequest()
    {
        $request = app()->getRequest();

        // get data from request
        $this->data = $request->data ? : [];

        // rewrite REST with "method" param
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        // switch by method
        // FIXME: hardcoded messages
        switch ($this->method) {
            case AbstractRequest::METHOD_POST:
                $this->result = $this->create();
                app()->getMessages()->addSuccess("Row was created");
                break;
            case AbstractRequest::METHOD_PATCH:
            case AbstractRequest::METHOD_PUT:
                $this->result = $this->update();
                app()->getMessages()->addSuccess("Row was updated");
                break;
            case AbstractRequest::METHOD_DELETE:
                $this->result = $this->delete();
                app()->getMessages()->addSuccess("Row was deleted");
                break;
            case AbstractRequest::METHOD_GET:
                $this->result = $this->read();

                // always HTML
                app()->useJson(false);

                // enable Layout for not AJAX request
                if (!app()->getRequest()->isXmlHttpRequest()) {
                    app()->useLayout(true);
                }
                break;
            default:
                throw new CrudException("Unsopported method");
                break;
        }
        return $this->result;
    }


    /**
     * setTable
     *
     * @param Db\Table $table
     * @return self
     */
    public function setTable(Db\Table $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * getTable
     *
     * @return Db\Table
     */
    public function getTable()
    {
        if (!$this->table) {
            $crudClass = get_called_class();
            $tableClass = substr($crudClass, 0, strrpos($crudClass, '\\', 1) + 1) . 'Table';

            /**
             * @var Db\Table $tableClass
             */
            $table = $tableClass::getInstance();

            $this->setTable($table);
        }
        return $this->table;
    }

    /**
     * getPrimaryKey
     *
     * @return array
     */
    protected function getPrimaryKey()
    {
        $primary = array_flip($this->getTable()->getPrimaryKey());
        return array_intersect_key($this->getData(), $primary);
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
     * getResult
     *
     * @return mixed
     */
    public function getResult()
    {
        if (!$this->result) {
            $this->result = $this->processRequest();
        }
        return $this->result;
    }

    /**
     * getResult
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * addError
     *
     * @param $field
     * @param $message
     * @return self
     */
    protected function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = array();
        }
        $this->errors[$field][] = $message;
        return $this;
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
     * @throws ValidationException
     */
    public function validate()
    {
        // validate entity
        // ...
        if (sizeof($this->errors)) {
            throw new ValidationException('Validation error, please check errors stack');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateCreate()
    {
        $this->validate();
    }

    /**
     * @throws ValidationException
     */
    public function validateUpdate($originalRow)
    {
        $this->validate();
    }


    /**
     * CRUD methods here
     */

    /**
     * @return boolean
     */
    public function create()
    {
        $this->validateCreate();

        $row = $this->getTable()->create();

        $row->setFromArray($this->data);
        return $row->save();
    }

    /**
     * @throws CrudException
     * @return \Bluz\Db\Row|null
     */
    public function read()
    {
        $primary = $this->getPrimaryKey();

        // nothing? ok, new row please
        if (!sizeof($primary)) {
            return null;
        }

        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new CrudException("Record not found");
        }

        return $row;
    }

    /**
     * @throws CrudException
     * @return boolean
     */
    public function update()
    {
        $primary = $this->getPrimaryKey();

        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new CrudException("Record not found");
        }

        $this->validateUpdate($row);

        $row->setFromArray($this->data);
        return $row->save();
    }

    /**
     * @throws CrudException
     * @return boolean
     */
    public function delete()
    {
        $primary = $this->getPrimaryKey();
        $row = $this->getTable()->findRow($primary);

        if (!$row) {
            throw new CrudException("Record not found");
        }
        return $row->delete();
    }
}
