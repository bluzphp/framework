<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Grid\Source;

use Bluz\Db;
use Bluz\Grid;
use Bluz\Proxy;

/**
 * SQL Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 */
class SelectSource extends AbstractSource
{
    /**
     * @var Db\Query\Select instance of select source
     */
    protected $source;

    /**
     * Set Select source
     *
     * @param  Db\Query\Select $source
     * @throws \Bluz\Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!$source instanceof Db\Query\Select) {
            throw new Grid\GridException("Source of `SelectSource` should be `Db\\Query\\Select` object");
        }
        $this->source = $source;

        return $this;
    }

    /**
     * Process
     *
     * @param  array $settings
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
        $type = Proxy\Db::getOption('connect', 'type');

        if (strtolower($type) == 'mysql') {
            // MySQL
            $select = $this->source->getQueryPart('select');
            $this->source->select('SQL_CALC_FOUND_ROWS ' . current($select));

            $totalSql = 'SELECT FOUND_ROWS()';
        } else {
            // other
            $totalSource = clone $this->source;
            $totalSource->select('COUNT(*)');

            $totalSql = $totalSource->getSql();
        }

        $data = [];
        $total = 0;

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(function() use (&$data, &$total, $totalSql) {
            $data = $this->source->execute();
            $total = Proxy\Db::fetchOne($totalSql);
        });

        $gridData = new Grid\Data($data);
        $gridData->setTotal($total);
        return $gridData;

    }
}
