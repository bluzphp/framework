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
 * Class Version
 * @see http://semver.org/
 * @package Bluz\Validator\Rule
 */
class Version extends AbstractRule
{
    /**
     * @var string
     */
    protected $template = '{{name}} must be a version';

    /**
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        $pattern = '/^[0-9]+\.[0-9]+\.[0-9]+([+-][^+-][0-9A-Za-z-.]*)?$/';

        return (bool) preg_match($pattern, $input);
    }
}
