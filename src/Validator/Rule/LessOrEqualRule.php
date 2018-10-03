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
 * Check for maximum
 *
 * @package Bluz\Validator\Rule
 */
class LessOrEqualRule extends LessRule
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
        return __('must be lower than or equals "%s"', $this->maxValue);
    }
}
