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

use Bluz\Db\Query\CompositeBuilder;

/**
 * Order Trait, required for:
 *  - SelectBuilder
 *  - UpdateBuilder
 *  - DeleteBuilder
 *
 * @category Bluz
 * @package  Db
 * @subpackage Query
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:38
 */
trait Where {

    /**
     * Specifies one or more restrictions to the query result
     * Replaces any previously specified restrictions, if any
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->where('u.id = ?', $id)
     *      ;
     * </code>
     *
     * @param mixed $condition The query restriction predicates
     * @return $this
     */
    public function where($condition)
    {
        $condition = $this->prepareCondition(func_get_args());

        return $this->addQueryPart('where', $condition);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u')
     *         ->from('users', 'u')
     *         ->where('u.username LIKE ?')
     *         ->andWhere('u.is_active = ?', 1);
     * </code>
     *
     * @param mixed $condition The query restriction predicates
     * @return $this
     */
    public function andWhere($condition)
    {
        $condition = $this->prepareCondition(func_get_args());

        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeBuilder && $where->getType() == 'AND') {
            $where->add($condition);
        } else {
            $where = new CompositeBuilder([$where, $condition]);
        }
        return $this->addQueryPart('where', $where);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.name')
     *         ->from('users', 'u')
     *         ->where('u.id = 1')
     *         ->orWhere('u.id = ?', 2);
     * </code>
     *
     * @param mixed $condition The query restriction predicates
     * @return $this
     */
    public function orWhere($condition)
    {
        $condition = $this->prepareCondition(func_get_args());

        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeBuilder && $where->getType() == 'OR') {
            $where->add($condition);
        } else {
            $where = new CompositeBuilder([$where, $condition], 'OR');
        }
        return $this->addQueryPart('where', $where);
    }
}
