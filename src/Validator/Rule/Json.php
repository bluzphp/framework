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
 * Class Json
 * @package Bluz\Validator\Rule
 */
class Json extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be a valid JSON string';

    /**
     * Check for valid JSON string
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) (json_decode($input));
    }
}
