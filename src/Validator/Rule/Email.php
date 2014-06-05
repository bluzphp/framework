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
 * Class Email
 * @package Bluz\Validator\Rule
 */
class Email extends AbstractRule
{
    /**
     * @var string
     */
    protected $template = '"{{name}}" must be valid email';

    /**
     * @var bool
     */
    protected $checkDomain;

    /**
     * @param bool $checkDomain
     */
    public function __construct($checkDomain = false)
    {
        $this->checkDomain = $checkDomain;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL)) {
            list(, $domain) = explode("@", $input, 2);
            if ($this->checkDomain && !checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
