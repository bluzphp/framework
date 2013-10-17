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
namespace Bluz\Db\Query\Traits;

/**
 * Set Trait, required for:
 *  - Insert Builder
 *  - Update  Builder
 *
 * @category Bluz
 * @package  Db
 * @subpackage Query
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:00
 */
trait Set {

    /**
     * Sets a new value for a column in a insert/update query
     *
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     * </code>
     *
     * @param string $key The column to set
     * @param string $value The value, expression, placeholder, etc
     * @return $this
     */
    public function set($key, $value)
    {
        $this->setParameter(null, $value);
        $key = $this->getAdapter()->quoteIdentifier($key);
        return $this->addQueryPart('set', $key .' = ?', true);
    }

    /**
     * setArray
     *
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->setArray([
     *              'password' => md5('password')
     *              'updated' => date('Y-m-d H:i:s')
     *          ])
     *         ->where('u.id = ?');
     * </code>
     *
     * @param array $data
     * @return $this
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
}
