<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Source;

use Bluz\Db;
use Bluz\Grid;
use Bluz\Grid\Data;
use Bluz\Proxy;

/**
 * SQL Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 *
 * @method   Db\Query\Select getSource()
 */
class SelectSource extends AbstractSource
{
    /**
     * Set Select source
     *
     * @param Db\Query\Select $source
     *
     * @return void
     * @throws Grid\GridException
     */
    public function setSource($source): void
    {
        if (!$source instanceof Db\Query\Select) {
            throw new Grid\GridException('Source of `SelectSource` should be `Db\\Query\\Select` object');
        }
        parent::setSource($source);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Grid\GridException
     * @throws Db\Exception\DbException
     */
    public function process(int $page, int $limit, array $filters = [], array $orders = []): Data
    {
        // process filters
        $this->applyFilters($filters);

        // process orders
        $this->applyOrders($orders);

        // process pages
        $this->getSource()->setLimit($limit);
        $this->getSource()->setPage($page);

        // prepare query
        $type = Proxy\Db::getOption('connect', 'type');

        if (strtolower($type) === 'mysql') {
            // MySQL
            $selectPart = $this->getSource()->getSelect();
            $selectPart[0] = 'SQL_CALC_FOUND_ROWS ' . $selectPart[0];
            $this->getSource()->select(...$selectPart);

            $totalSql = 'SELECT FOUND_ROWS()';
        } else {
            // other
            $totalSource = clone $this->getSource();
            $totalSource->select('COUNT(*)');

            $totalSql = $totalSource->getSql();
        }

        $data = [];
        $total = 0;

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(
            function () use (&$data, &$total, $totalSql) {
                $data = $this->source->execute();
                $total = (int)Proxy\Db::fetchOne($totalSql);
            }
        );

        $gridData = new Data($data);
        $gridData->setTotal($total);
        return $gridData;
    }

    /**
     * Apply filters to Select
     *
     * @param array $settings
     *
     * @return void
     * @throws Grid\GridException
     */
    private function applyFilters(array $settings): void
    {
        foreach ($settings as $column => $filters) {
            foreach ($filters as $filter => $value) {
                if ($filter === Grid\Grid::FILTER_LIKE) {
                    $value = '%' . $value . '%';
                }
                $this->getSource()->andWhere($column . ' ' . $this->filters[$filter] . ' ?', $value);
            }
        }
    }

    /**
     * Apply order to Select
     *
     * @param array $settings
     *
     * @return void
     * @throws Grid\GridException
     */
    private function applyOrders(array $settings): void
    {
        // Obtain a list of columns
        foreach ($settings as $column => $order) {
            $this->getSource()->addOrderBy($column, $order);
        }
    }
}
