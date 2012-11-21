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

use Bluz\Grid;

/**
 * Array Source Adapter for Grid package
 *
 * @category Bluz
 * @package  Grid
 *
 * @author   Anton Shevchuk
 * @created  27.08.12 10:11
 */
class ArraySource extends AbstractSource
{
    /**
     * setSource
     *
     * @param $source
     * @throws Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!is_array($source) && !($source instanceof \ArrayAccess)) {
            throw new Grid\GridException("Source of ArraySource should be array or implement ArrayAccess interface");
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
        $data = $this->source;

        // process filters
        if (!empty($settings['filters'])) {
            $data = array_filter($data, function($row) use ($settings){
                foreach ($settings['filters'] as $column => $filters) {
                    foreach ($filters as $filter => $value) {
                        // switch statement for $filter
                        switch ($filter) {
                            case Grid\Grid::FILTER_EQ:
                                if ($row[$column] != $value) return false;
                                break;
                            case Grid\Grid::FILTER_NE:
                                if ($row[$column] == $value) return false;
                                break;
                            case Grid\Grid::FILTER_GT:
                                if ($row[$column] <= $value) return false;
                                break;
                            case Grid\Grid::FILTER_GE:
                                if ($row[$column] < $value) return false;
                                break;
                            case Grid\Grid::FILTER_LT:
                                if ($row[$column] >= $value) return false;
                                break;
                            case Grid\Grid::FILTER_LE:
                                if ($row[$column] > $value) return false;
                                break;
                        }
                    }
                    return true;
                }
            });
        }

        // process orders
        if (!empty($settings['orders'])) {
            // Create empty column stack
            $orders = [];
            foreach ($settings['orders'] as $column => $order) {
                $orders[$column] = [];
            }

            // Obtain a list of columns
            foreach ($data as $key => $row) {
                foreach ($settings['orders'] as $column => $order) {
                    $orders[$column][$key] = $row[$column];
                }
            }
            // Prepare array of arguments
            $funcArgs = [];
            foreach ($settings['orders'] as $column => $order) {
                $funcArgs[] = $orders[$column];
                $funcArgs[] = ($order==Grid\Grid::ORDER_ASC) ? SORT_ASC : SORT_DESC;
            }
            $funcArgs[] = &$data;

            // Sort the data with volume descending, edition ascending
            // Add $data as the last parameter, to sort by the common key
            call_user_func_array('array_multisort', $funcArgs);
        }

        $total = sizeof($data);

        // process pages
        $data = array_slice($data, ($settings['limit']*($settings['page']-1)), $settings['limit']);

        return new Grid\Data($data, $total);
    }
}
