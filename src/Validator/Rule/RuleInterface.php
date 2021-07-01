<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\ValidatorInterface;

/**
 * Validator Rule Interface
 *
 * @package  Bluz\Validator\Rule
 * @author   Anton Shevchuk
 */
interface RuleInterface extends ValidatorInterface
{
    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Set error template
     *
     * @param  string $description
     *
     * @return self
     */
    public function setDescription(string $description): RuleInterface;
}
