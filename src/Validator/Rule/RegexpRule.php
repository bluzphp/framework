<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check string by regular expression
 *
 * @package Bluz\Validator\Rule
 */
class RegexpRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must validate with regular expression rule';

    /**
     * @var string regular expression for check string
     */
    protected $regexp;

    /**
     * Check string by regular expression
     *
     * @param string $regexp
     */
    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * Check string by regular expression
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return (bool)preg_match($this->regexp, $input);
    }
}
