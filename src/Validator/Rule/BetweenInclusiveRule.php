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
 * Check for value in range between minimum and maximum values
 *
 * @package Bluz\Validator\Rule
 */
class BetweenInclusiveRule extends BetweenRule
{
    /**
     * @var bool
     */
    protected $inclusive = true;

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        $min = $this->minValue;
        $max = $this->maxValue;

        if ($min instanceof \DateTime) {
            $min = date_format($min, 'r');
        }
        if ($max instanceof \DateTime) {
            $max = date_format($max, 'r');
        }

        return __('must be inclusive between "%1s" and "%2s"', $min, $max);
    }
}
