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
namespace Bluz\Validator\Rule;

/**
 * Class AbstractCtypeRule
 * @package Bluz\Validator\Rule
 */
abstract class AbstractCtypeRule extends AbstractFilterRule
{
    /**
     * Filter input data
     * @param string $input
     * @return string
     */
    protected function filter($input)
    {
        $input = parent::filter((string) $input);
        return preg_replace('/\s/', '', $input);
    }
}
