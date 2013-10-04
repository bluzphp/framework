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
 * @author   AntonShevchuk
 * @created  15.08.12 15:37
 */
class Table extends AbstractCrud
{
    /**
     * @var \Bluz\Db\Table
     */
    protected $table;

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
     * CRUD methods here
     */

    /**
     * @param mixed $primary
     * @return \Bluz\Db\Row|null
     */
    public function readOne($primary)
    {
        // nothing? ok, new row please
        if (!sizeof($primary)) {
            return null;
        }

        return $this->getTable()->findRow($primary);
    }
}
