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
class SelectSource extends AbstractSource
{
    /**
     * @var Db\Query\Select
     */
    protected $source;

    /**
     * setSource
     *
     * @param $source
     * @throws \Bluz\Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!$source instanceof Db\Query\Select) {
            throw new Grid\GridException("Source of SelectSource should be Db\\Query\\Select object");
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
        if (!empty($settings['filters'])) {
            foreach ($settings['filters'] as $column => $filters) {
                foreach ($filters as $filter => $value) {
                    if ($filter == Grid\Grid::FILTER_LIKE) {
                        $value = '%'.$value.'%';
                    }
                    $this->source->andWhere($column .' '. $this->filters[$filter] .' ?', $value);
                }
            }
        }

        // process orders
        if (!empty($settings['orders'])) {
            // Obtain a list of columns
            foreach ($settings['orders'] as $column => $order) {
                $this->source->addOrderBy($column, $order);
            }
        }

        // process pages
        $this->source->setLimit($settings['limit']);
        $this->source->setPage($settings['page']);

        // prepare query
        $connect = Application::getInstance()->getConfigData('db', 'connect');
        if (strtolower($connect['type']) == 'mysql') { // MySQL
            $select = $this->source->getQueryPart('select');
            $select[0] = 'SQL_CALC_FOUND_ROWS ' . current($select);
            $this->source->select($select);

            // run queries
            $data = $this->source->execute();
            $total = Db\Db::getDefaultAdapter()->fetchOne('SELECT FOUND_ROWS()');
        } else { // other
            $totalSource = clone $this->source;
            $totalSource->select('COUNT(*)');

            // run queries
            $data = $this->source->execute();
            $total = $totalSource->execute();
        }
        $gridData = new Grid\Data($data);
        $gridData->setTotal($total);
        return $gridData;

    }
}
