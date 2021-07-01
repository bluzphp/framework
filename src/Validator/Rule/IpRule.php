<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;

/**
 * Check for IP
 *
 * Strict mode disabled for this file, because function long2ip() was changed in PHP 7.1
 *
 * @package Bluz\Validator\Rule
 */
class IpRule extends AbstractRule
{
    /**
     * @var integer setup options
     */
    protected $options;

    /**
     * @var array network range
     */
    protected $networkRange;

    /**
     * Setup validation rule
     *
     * @param mixed $options
     *
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($options = null)
    {
        if (is_int($options)) {
            $this->options = $options;
            return;
        }

        $this->networkRange = $this->parseRange($options);
    }

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->verifyAddress($input) && $this->verifyNetwork($input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        if (!empty($this->networkRange)) {
            $message = $this->networkRange['min'];
            if (isset($this->networkRange['max'])) {
                $message .= '-' . $this->networkRange['max'];
            } else {
                $message .= '/' . long2ip((string)bindec($this->networkRange['mask']));
            }
            return __('must be an IP address in the "%s" range', $message);
        }
        return __('must be an IP address');
    }

    /**
     * Parse IP range
     *
     * @param  string $input
     *
     * @return array|null
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    protected function parseRange($input): ?array
    {
        if (
            $input === null || $input === '*' || $input === '*.*.*.*'
            || $input === '0.0.0.0-255.255.255.255'
        ) {
            return null;
        }

        $range = ['min' => null, 'max' => null, 'mask' => null];

        if (strpos($input, '-') !== false) {
            [$range['min'], $range['max']] = explode('-', $input);
        } elseif (strpos($input, '*') !== false) {
            $this->parseRangeUsingWildcards($input, $range);
        } elseif (strpos($input, '/') !== false) {
            $this->parseRangeUsingCidr($input, $range);
        } else {
            throw new ComponentException('Invalid network range');
        }

        if (!$this->verifyAddress($range['min'])) {
            throw new ComponentException('Invalid network range');
        }

        if (isset($range['max']) && !$this->verifyAddress($range['max'])) {
            throw new ComponentException('Invalid network range');
        }

        return $range;
    }

    /**
     * Fill address
     *
     * @param string $input
     * @param string $char
     */
    protected function fillAddress(&$input, $char = '*'): void
    {
        while (substr_count($input, '.') < 3) {
            $input .= '.' . $char;
        }
    }

    /**
     * Parse range using wildcards
     *
     * @param string $input
     * @param array  $range
     */
    protected function parseRangeUsingWildcards($input, &$range): void
    {
        $this->fillAddress($input);

        $range['min'] = str_replace('*', '0', $input);
        $range['max'] = str_replace('*', '255', $input);
    }

    /**
     * Parse range using Classless Inter-Domain Routing (CIDR)
     *
     * @param  string $input
     * @param  array  $range
     *
     * @throws \Bluz\Validator\Exception\ComponentException
     * @link http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
     */
    protected function parseRangeUsingCidr($input, &$range): void
    {
        $input = explode('/', $input);
        $this->fillAddress($input[0], '0');

        $range['min'] = $input[0];
        $isAddressMask = strpos($input[1], '.') !== false;

        if ($isAddressMask && $this->verifyAddress($input[1])) {
            $range['mask'] = sprintf('%032b', ip2long($input[1]));
            return;
        }

        if ($isAddressMask || $input[1] < 8 || $input[1] > 30) {
            throw new ComponentException('Invalid network mask');
        }

        $range['mask'] = sprintf('%032b', ip2long(long2ip(~(2 ** (32 - $input[1]) - 1))));
    }

    /**
     * Verify IP address
     *
     * @param  string $address
     *
     * @return bool
     */
    protected function verifyAddress($address): bool
    {
        return (bool)filter_var(
            $address,
            FILTER_VALIDATE_IP,
            [
                'flags' => $this->options
            ]
        );
    }

    /**
     * Verify Network by mask
     *
     * @param  string $input
     *
     * @return bool
     */
    protected function verifyNetwork($input): bool
    {
        if ($this->networkRange === null) {
            return true;
        }

        if (isset($this->networkRange['mask'])) {
            return $this->belongsToSubnet($input);
        }

        $input = sprintf('%u', ip2long($input));

        $min = sprintf('%u', ip2long($this->networkRange['min']));
        $max = sprintf('%u', ip2long($this->networkRange['max']));

        return ($input >= $min) && ($input <= $max);
    }

    /**
     * Check subnet
     *
     * @param  string $input
     *
     * @return bool
     */
    protected function belongsToSubnet($input): bool
    {
        $range = $this->networkRange;
        $min = sprintf('%032b', ip2long($range['min']));
        $input = sprintf('%032b', ip2long($input));

        return ($input & $range['mask']) === ($min & $range['mask']);
    }
}
