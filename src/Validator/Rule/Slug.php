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
 * Class Slug
 * @package Bluz\Validator\Rule
 */
class Slug extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be a valid slug';

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        if (strstr($input, '--')) {
            return false;
        }

        if (!preg_match('/^[0-9a-z\-]+$/', $input)) {
            return false;
        }

        if (preg_match('/^-|-$/', $input)) {
            return false;
        }

        return true;
    }
}
