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
 * Class CreditCard
 * @package Bluz\Validator\Rule
 */
class CreditCard extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be a valid Credit Card number';

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        $input = preg_replace('([ \.-])', '', $input);

        if (!is_numeric($input)) {
            return false;
        }

        return $this->verifyMod10($input);
    }

    /**
     * Verify by Mod10
     * @link https://en.wikipedia.org/wiki/Luhn_algorithm
     * @param string $input
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
