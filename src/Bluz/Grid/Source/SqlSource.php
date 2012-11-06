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
namespace Bluz\Grid\Source;

use Bluz\Application;
use Bluz\Db;
use Bluz\Grid;

/**
 * SQL Source Adapter for Grid package
 *
 * @category Bluz
 * @package  Grid
 *
 * @author   Anton Shevchuk
 * @created  27.08.12 10:10
 */
class SqlSource extends AbstractSource
{
    /**
     * setSource
     *
     * @param $source
     * @throws \Bluz\Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!is_string($source)) {
            throw new Grid\GridException("Source of SqlSource should be string with SQL query");
        }
        $this->source = $source;

        return $this;
    }

    /**
     * process
     *
     * @param array $settings
     * @return \Bluz\Grid\Data
     */
    public function process(array $settings = [])
    {
        // process filters
        $where = [];
        if (!empty($settings['filters'])) {
            foreach ($settings['filters'] as $column => $filters) {
                foreach ($filters as $filter => $value) {
                    // switch statement for $filter
                    switch ($filter) {
                        case Grid\Grid::FILTER_EQ:
                            $where[] = $column .' = '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                        case Grid\Grid::FILTER_NE:
                            $where[] = $column .' != '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                        case Grid\Grid::FILTER_GT:
                            $where[] = $column .' > '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                        case Grid\Grid::FILTER_GE:
                            $where[] = $column .' >= '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                        case Grid\Grid::FILTER_LT:
                            $where[] = $column .' < '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                        case Grid\Grid::FILTER_LE:
                            $where[] = $column .' <= '. Db\Db::getDefaultAdapter()->quote($value);
                            break;
                    }
                }
            }
        }

        // process orders
        $orders = [];
        if (!empty($settings['orders'])) {
            // Obtain a list of columns
            foreach ($settings['orders'] as $column => $order) {
                $orders[] = $column .' '. $order;
            }
        }

        // process pages
        $limit = ' LIMIT '. ($settings['page']-1)*$settings['limit'] .', '. $settings['limit'];

        // prepare query
        $connect = Application::getInstance()->getConfigData('db', 'connect');
        if (strtolower($connect['type']) == 'mysql') { // MySQL
            $dataSql = preg_replace('/SELECT (.*?) FROM/is', 'SELECT SQL_CALC_FOUND_ROWS $1 FROM', $this->source);
            $countSql = 'SELECT FOUND_ROWS()';
        } else { // other
            $dataSql = $this->source;
            $countSql = preg_replace('/SELECT (.*?) FROM/is', 'SELECT COUNT(*) FROM', $this->source);
            if (sizeof($where)) {
                $countSql .= ' WHERE '. (join(' AND ', $where));
            }
        }

        if (sizeof($where)) {
            $dataSql .= ' WHERE '. (join(' AND ', $where));
        }
        if (sizeof($orders)) {
            $dataSql .= ' ORDER BY '. (join(', ', $orders));
        }
        $dataSql .= $limit;

        // run queries
        $data = Db\Db::getDefaultAdapter()->fetchAll($dataSql);
        $total = Db\Db::getDefaultAdapter()->fetchOne($countSql);
        return new Grid\Data($data, $total);
    }
}
