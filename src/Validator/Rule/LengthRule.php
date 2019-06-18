<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;
use Countable;

/**
 * Check for length in range between minimum and maximum values
 *
 * @package Bluz\Validator\Rule
 */
class LengthRule extends AbstractCompareRule
{
    /**
     * @var integer minimum value
     */
    protected $minValue;

    /**
     * @var integer maximum value
     */
    protected $maxValue;

    /**
     * @var bool
     */
    protected $inclusive = true;

    /**
     * Setup validation rule
     *
     * @param  integer|null $min
     * @param  integer|null $max
     *
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($min = null, $max = null)
    {
        $this->minValue = $min;
        $this->maxValue = $max;

        if (!is_numeric($min) && null !== $min) {
            throw new ComponentException(
                __('"%s" is not a valid numeric length', $min)
            );
        }

        if (!is_numeric($max) && null !== $max) {
            throw new ComponentException(
                __('"%s" is not a valid numeric length', $max)
            );
        }

        if (null !== $min && null !== $max && $min > $max) {
            throw new ComponentException(
                __('"%s" cannot be less than "%s" for validation', $min, $max)
            );
        }
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
        if (!$length = $this->extractLength($input)) {
            return false;
        }

        return (null === $this->minValue || $this->less($this->minValue, $length))
            && (null === $this->maxValue || $this->less($length, $this->maxValue));
    }

    /**
     * Extract length
     *
     * @param  string|object $input
     *
     * @return integer|false
     */
    protected function extractLength($input)
    {
        if (is_string($input)) {
            return mb_strlen($input, mb_detect_encoding($input));
        }

        if (is_array($input) || $input instanceof Countable) {
            return count($input);
        }

        if (is_object($input)) {
            return count(get_object_vars($input));
        }
        return false;
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        if (!$this->minValue) {
            return __('must have a length lower than "%d"', $this->maxValue);
        }
        if (!$this->maxValue) {
            return __('must have a length greater than "%d"', $this->minValue);
        }
        if ($this->minValue === $this->maxValue) {
            return __('must have a length "%d"', $this->minValue);
        }
        return __('must have a length between "%d" and "%d"', $this->minValue, $this->maxValue);
    }
}
