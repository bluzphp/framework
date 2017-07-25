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
 * Check for email
 *
 * @package Bluz\Validator\Rule
 */
class EmailRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be a valid email address';

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
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL)) {
            list(, $domain) = explode('@', $input, 2);
            if ($this->checkDns) {
                return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
            }
            return true;
        }
        return false;
    }
}
