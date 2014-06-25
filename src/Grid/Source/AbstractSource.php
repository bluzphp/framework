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

use Bluz\Grid\Grid;

/**
 * Adapter
 *
 * @package  Bluz\Grid
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 11:52
 */
abstract class AbstractSource
{
    /**
     * Source for build grid
     * @var mixed
     */
    protected $source;

    /**
     * Available filters
     * @var array
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
     * Setup adapter source
     *
     * @param mixed $source
     * @return self
     */
    abstract public function setSource($source);

    /**
     * Process source
     *
     * @param array $settings
     * @return \Bluz\Grid\Data
     */
    abstract public function process(array $settings = []);
}
