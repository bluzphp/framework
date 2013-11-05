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

use Bluz\Request\AbstractRequest;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Translator\Translator;

/**
 * Crud
 *
 * @category Bluz
 * @package  Crud
 *
 * @author   Anton Shevchuk
 * @created  23.09.13 15:33
 */
abstract class AbstractCrud
{
    /**
     * Default limit for pagination
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Errors stack
     * @var array
     */
    protected $errors = array();

    /**
     * getInstance
     *
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Return primary key signature
     *
     * @return array
     */
    abstract function getPrimaryKey();

    /**
     * get item by primary key(s)
     *
     * @param mixed $primary
     * @throws NotImplementedException
     * @return mixed
     */
    public function readOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * list of items
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @throws NotImplementedException
     * @return mixed
     */
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, $params = array())
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
    public function createOne($data)
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
    public function createSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * update item
     *
     * @param mixed $primary
     * @param array $data
     * @throws NotImplementedException
     * @return integer
     */
    public function updateOne($primary, $data)
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
    public function updateSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * delete item
     *
     * @param mixed $primary
     * @throws NotImplementedException
     * @return integer
     */
    public function deleteOne($primary)
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
    public function deleteSet($data)
    {
        throw new NotImplementedException();
    }

    /**
     * validate
     *
     * @param null $primary
     * @param array $data
     * @return boolean
     */
    public function validate($primary, $data)
    {
        return !$this->hasErrors();
    }

    /**
     * createValidation
     *
     * @param array $data
     * @return boolean
     */
    public function validateCreate($data)
    {
        return !$this->hasErrors();
    }

    /**
     * updateValidation
     *
     * @param mixed $primary
     * @param array $data
     * @return boolean
     */
    public function validateUpdate($primary, $data)
    {
        return !$this->hasErrors();
    }

    /**
     * Add new errors to stack
     *
     * @param $message
     * @param $field
     * @return self
     */
    protected function addError($message, $field)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = array();
        }
        $this->errors[$field][] = Translator::translate($message);
        return $this;
    }

    /**
     * Get errors stack
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Has errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return sizeof($this->errors);
    }

    /**
     * Check errors stack and throw 
     * @throws ValidationException
     */
    public function checkErrors()
    {
        if ($this->hasErrors()) {
            throw new ValidationException('Your request contains error(s) please fix them before try again');
        }
    }
}
