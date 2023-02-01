<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator;

use Bluz\Validator\Exception\ValidatorException;

/**
 * Validator Rule Interface
 *
 * @package  Bluz\Validator\Rule
 * @author   Anton Shevchuk
 */
interface ValidatorInterface
{
    /**
     * Check input data
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool;

    /**
     * Assert
     *
     * @param mixed $input
     *
     * @throws ValidatorException
     */
    public function assert($input): void;

    /**
     * Invoke
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function __invoke($input): bool;
}
