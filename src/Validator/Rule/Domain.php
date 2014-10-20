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
 * Class Domain
 * @package Bluz\Validator\Rule
 */
class Domain extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be a valid domain';

    /**
     * @var bool Check DNS record
     */
    protected $checkDns;

    /**
     * Setup validation rule
     * @param bool $checkDns
     */
    public function __construct($checkDns = false)
    {
        $this->checkDns = $checkDns;
    }

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
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
