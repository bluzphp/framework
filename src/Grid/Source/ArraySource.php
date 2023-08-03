<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Source;

use Bluz\Grid;
use Bluz\Grid\Data;

/**
 * Array Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 *
 * @method   array getSource()
 */
class ArraySource extends AbstractSource
{
    /**
     * Set array source
     *
     * @param array $source
     *
     * @return void
     * @throws Grid\GridException
     */
    public function setSource($source): void
    {
        if (!is_array($source) && !($source instanceof \ArrayAccess)) {
            throw new Grid\GridException('Source of `ArraySource` should be array or implement ArrayAccess interface');
        }
        parent::setSource($source);
    }

    /**
     * @inheritDoc
     * @throws Grid\GridException
     */
    public function process(int $page, int $limit, array $filters = [], array $orders = []): Data
    {
        // process filters
        $this->applyFilters($filters);

        // process orders
        $this->applyOrders($orders);

        $data = $this->getSource();

        $total = count($data);

        // process pages
        $data = \array_slice($data, $limit * ($page - 1), $limit);

        $gridData = new Data($data);
        $gridData->setTotal($total);
        return $gridData;
    }

    /**
     * Apply filters to array
     *
     * @param array $settings
     *
     * @return void
     * @throws Grid\GridException
     */
    private function applyFilters(array $settings): void
    {
        $data = $this->getSource();
        $data = array_filter(
            $data,
            function ($row) use ($settings) {
                foreach ($settings as $column => $filters) {
                    foreach ($filters as $filter => $value) {
                        // switch statement for filter
                        switch ($filter) {
                            case Grid\Grid::FILTER_EQ:
                                if ($row[$column] != $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_NE:
                                if ($row[$column] == $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_GT:
                                if ($row[$column] <= $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_GE:
                                if ($row[$column] < $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_LT:
                                if ($row[$column] >= $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_LE:
                                if ($row[$column] > $value) {
                                    return false;
                                }
                                break;
                            case Grid\Grid::FILTER_LIKE:
                                if (!preg_match('/' . $value . '/', $row[$column])) {
                                    return false;
                                }
                                break;
                        }
                    }
                }
                return true;
            }
        );
        $this->setSource($data);
    }

    /**
     * Apply order to array
     *
     * @param array $settings
     *
     * @return void
     * @throws Grid\GridException
     */
    private function applyOrders(array $settings): void
    {
        $data = $this->getSource();
        // Create empty column stack
        $orders = [];
        foreach ($settings as $column => $order) {
            $orders[$column] = [];
        }

        // Obtain a list of columns
        foreach ($data as $key => $row) {
            foreach ($settings as $column => $order) {
                $orders[$column][$key] = $row[$column];
            }
        }

        // Prepare array of arguments
        $funcArgs = [];
        foreach ($settings as $column => $order) {
            $funcArgs[] = $orders[$column];
            $funcArgs[] = ($order === Grid\Grid::ORDER_ASC) ? SORT_ASC : SORT_DESC;
        }
        $funcArgs[] = &$data;

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
        array_multisort(...$funcArgs);
        $this->setSource($data);
    }
}
