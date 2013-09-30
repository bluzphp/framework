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
     * get item
     *
     * @param $id
     * @throws NotImplementedException
     * @return mixed
     */
    public function readOne($id)
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
    public function readSet($offset = 0, $limit = self::DEFAULT_LIMIT, array $params = array())
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
    public function createOne(array $data)
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
    public function createSet(array $data)
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
    public function updateOne($id, array $data)
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
    public function updateSet(array $data)
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
    public function deleteOne($id)
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
    public function deleteSet(array $data)
    {
        throw new NotImplementedException();
    }
}
