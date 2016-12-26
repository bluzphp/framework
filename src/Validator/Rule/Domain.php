<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for domain
 *
 * @package Bluz\Validator\Rule
 */
class Domain extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a valid domain';

    /**
     * @var bool check DNS record flag
     */
    protected $checkDns;

    /**
     * Setup validation rule
     *
     * @param bool $checkDns
     */
    public function __construct($checkDns = false)
    {
        $this->checkDns = $checkDns;
    }

    /**
     * Check input data
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
    {
        $input = (string) $input;
        // check by regular expression
        if (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $input)
            && preg_match("/^.{1,253}$/", $input)
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $input)) {
            // check by DNS record
            if ($this->checkDns) {
                return checkdnsrr($input, "A");
            } else {
                return true;
            }
        }
        return false;
    }
}
