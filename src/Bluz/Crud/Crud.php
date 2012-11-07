<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
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

use Bluz\Application;
use Bluz\Request\AbstractRequest;

/**
 * Crud
 *
 * @category Bluz
 * @package  Crud
 *
 * @author   AntonShevchuk
 * @created  15.08.12 15:37
 */
class Crud
{
    use \Bluz\Package;

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
     * Result of last CRUD operation (on of get/put/post/delete)
     * @var mixed
     */
    protected $result;

    /**
     * @return Crud
     */
    public function processRequest()
    {
        $request = $this->getApplication()->getRequest();

        // get data from request
        $this->data = $request->getParam('data')?:[];

        // rewrite REST with "method" param
        $this->method = $request->getParam('_method', $request->getMethod());

        // switch by method
        switch ($this->method) {
            case AbstractRequest::METHOD_POST:
                $this->result = $this->create();
                break;
            case AbstractRequest::METHOD_PUT:
                $this->result = $this->update();
                break;
            case AbstractRequest::METHOD_DELETE:
                $this->result = $this->delete();
                break;
            case AbstractRequest::METHOD_GET:
            default:
                $this->result = $this->get();
                break;
        }

        return $this;
    }

    /**
     * processView
     * 
     * @return mixed
     */
    public function processController()
    {
        $this->getApplication()->useJson(true);
        try {
            $result = $this->processRequest()
                ->getResult();
        } catch (\Bluz\Crud\CrudException $e) {
            // all "not found" errors
            $this->getApplication()->getMessages()->addError($e->getMessage());
            return false;
        } catch (\Bluz\Crud\ValidationException $e) {
            // validate errors
            $this->getApplication()->getMessages()->addError("Please fix all errors");
            return [
                'errors' => $this->getErrors(),
                'callback' => 'validateForm'
            ];
        }

        // switch statement for $this->getMethod()
        switch ($this->getMethod()) {
            case AbstractRequest::METHOD_POST:
                // CREATE record
                $this->getApplication()->getMessages()->addSuccess("Row was created");
                $this->getApplication()->reload();
                break;
            case AbstractRequest::METHOD_PUT:
                // UPDATE record
                $this->getApplication()->getMessages()->addSuccess("Row was updated");
                $this->getApplication()->reload();
                break;
            case AbstractRequest::METHOD_DELETE:
                // DELETE record
                $this->getApplication()->getMessages()->addSuccess("Row was deleted");
                $this->getApplication()->reload();
                break;
            case AbstractRequest::METHOD_GET:
            default:
                // always HTML
                $this->getApplication()->useJson(false);

                // enable Layout for not AJAX request
                if (!$this->getApplication()->getRequest()->isXmlHttpRequest()) {
                    $this->getApplication()->useLayout(true);
                }

                // EDIT or CREATE form
                if ($result instanceof \Bluz\Db\Row) {
                    // edit form
                    return [
                        'row' => $result,
                        'method' => 'put'
                    ];
                } else {
                    // create form
                    return [
                        'row' => $this->getTable()->create(),
                        'method' => 'post'
                    ];
                }
                break;
        }
    }

    /**
     * setTable
     *
     * @param \Bluz\Db\Table $table
     * @return self
     */
    public function setTable(\Bluz\Db\Table $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * getTable
     *
     * @return \Bluz\Db\Table
     */
    public function getTable()
    {
        if (!$this->table) {
            $crudClass = get_called_class();
            $tableClass = substr($crudClass, 0, strrpos($crudClass, '\\', 1)+1) . 'Table';

            /**
             * @var \Bluz\Db\Table $tableClass
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
        $primary = array_flip( $this->getTable()->getPrimaryKey() );
        return array_intersect_key( $this->getData(), $primary );
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
     * @throws \Bluz\Crud\ValidationException
     */
    public function validate()
    {
        // validate entity
        // ...
        if (sizeof($this->errors)) {
            throw new \Bluz\Crud\ValidationException('Validation error, please check errors stack');
        }
    }

    /**
     * @throws \Bluz\Crud\ValidationException
     */
    public function validateCreate()
    {
        $this->validate();
    }

    /**
     * @throws \Bluz\Crud\ValidationException
     */
    public function validateUpdate($originalRow)
    {
        $this->validate();
    }

    /**
     * @throws CrudException
     * @return \Bluz\Db\Row|null
     */
    public function get()
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
