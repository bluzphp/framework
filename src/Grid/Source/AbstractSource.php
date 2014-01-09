<?php
/**
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
 * @category Bluz
 * @package  Grid
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 11:52
 */
abstract class AbstractSource
{
    /**
     * @var mixed
     */
    protected $source;

    /**
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
     * @param $source
     * @return AbstractSource
     */
    abstract public function setSource($source);

    /**
     * Process source
     *
     * @param array $settings
     * @return AbstractSource
     */
    abstract public function process(array $settings = []);
}
