<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use DateTime;

/**
 * Check for date
 *
 * @package Bluz\Validator\Rule
 */
class DateRule extends AbstractRule
{
    /**
     * @var string date format
     */
    protected $format = null;

    /**
     * Setup validation rule
     *
     * @param string $format
     */
    public function __construct($format = null)
    {
        $this->format = $format;
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
        if ($input instanceof DateTime) {
            return true;
        }

        if (!is_string($input)) {
            return false;
        }

        if (null === $this->format) {
            return false !== strtotime($input);
        }

        $dateFromFormat = DateTime::createFromFormat($this->format, $input);

        return $dateFromFormat
            && $input === date($this->format, $dateFromFormat->getTimestamp());
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        if ($this->format) {
            return __('must be a valid date. Sample format: "%s"', $this->format);
        }
        return __('must be a valid date');
    }
}
