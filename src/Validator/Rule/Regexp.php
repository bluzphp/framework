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
 * Class Regexp
 * @package Bluz\Validator\Rule
 */
class Regexp extends AbstractRule
{
    /**
     * @var string
     */
    protected $template = '{{name}} must validate with regular expression rule';

    /**
     * @var string
     */
    protected $regexp;

    /**
     * @param string $regexp
     * @return self
     */
    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) preg_match($this->regexp, $input);
    }
}
