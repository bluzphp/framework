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
 * Check for version format
 *
 * @package Bluz\Validator\Rule
 * @link    http://semver.org/
 */
class Version extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a version';

    /**
     * Check for version format
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
    {
        $pattern = '/^[0-9]+\.[0-9]+\.[0-9]+([+-][^+-][0-9A-Za-z-.]*)?$/';

        return (bool) preg_match($pattern, $input);
    }
}
