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
 * Check credit card number by Mod10 algorithm
 *
 * @package Bluz\Validator\Rule
 * @link    https://en.wikipedia.org/wiki/Luhn_algorithm
 */
class CreditCard extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a valid Credit Card number';

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        $input = preg_replace('([ \.-])', '', $input);

        if (!is_numeric($input)) {
            return false;
        }

        return $this->verifyMod10($input);
    }

    /**
     * Verify by Mod10
     *
     * @param  string $input
     *
     * @return bool
     */
    private function verifyMod10($input)
    {
        $sum = 0;
        $input = strrev($input);
        for ($i = 0; $i < strlen($input); $i++) {
            $current = substr($input, $i, 1);
            if ($i % 2 == 1) {
                $current *= 2;
                if ($current > 9) {
                    $firstDigit = $current % 10;
                    $secondDigit = ($current - $firstDigit) / 10;
                    $current = $firstDigit + $secondDigit;
                }
            }
            $sum += $current;
        }

        return ($sum % 10 == 0);
    }
}
