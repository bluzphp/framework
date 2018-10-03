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
 * Check for minimum
 *
 * @package Bluz\Validator\Rule
 */
class MoreOrEqualRule extends MoreRule
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
        return __('must be greater than or equals "%s"', $this->minValue);
    }
}
