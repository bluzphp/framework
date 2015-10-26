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
 * Abstract ctype rules
 *
 * @package Bluz\Validator\Rule
 * @link    http://php.net/manual/ru/book.ctype.php
 */
abstract class AbstractCtypeRule extends AbstractFilterRule
{
    /**
     * Filter input data
     *
     * @param  string $input
     * @return string
     */
    protected function filter($input)
    {
        $input = parent::filter((string) $input);
        return preg_replace('/\s/', '', $input);
    }
}
