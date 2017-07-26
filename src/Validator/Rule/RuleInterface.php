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
 * Validator Rule Interface
 *
 * @package  Bluz\Validator\Rule
 * @author   Anton Shevchuk
 */
interface RuleInterface
{
    /**
     * Check input data
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool;

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription() : string;

    /**
     * Set error template
     *
     * @param  string $description
     *
     * @return self
     */
    public function setDescription(string $description);

    /**
     * Invoke
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function __invoke($input): bool;

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString();
}
