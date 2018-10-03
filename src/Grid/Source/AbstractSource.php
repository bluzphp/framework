<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Source;

use Bluz\Grid\Data;
use Bluz\Grid\Grid;
use Bluz\Grid\GridException;

/**
 * Adapter
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 */
abstract class AbstractSource
{
    /**
     * @var mixed source for build grid
     */
    protected $source;

    /**
     * @var array available filters
     */
    protected $filters = [
        Grid::FILTER_EQ => '=',
        Grid::FILTER_NE => '!=',
        Grid::FILTER_GT => '>',
        Grid::FILTER_GE => '>=',
        Grid::FILTER_LT => '<',
        Grid::FILTER_LE => '<=',
        Grid::FILTER_LIKE => 'like',
    ];

    /**
     * Process source
     *
     * @param int   $page
     * @param int   $limit
     * @param array $filters
     * @param array $orders
     *
     * @return Data
     */
    abstract public function process(int $page, int $limit, array $filters = [], array $orders = []): Data;

    /**
     * Setup source adapter
     *
     * @param  mixed $source
     *
     * @return void
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * Get source adapter
     *
     * @return mixed
     * @throws GridException
     */
    public function getSource()
    {
        if (!$this->source) {
            throw new GridException('Source Adapter should be initialized by `setSource` method');
        }
        return $this->source;
    }
}
