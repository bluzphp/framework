<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ValidatorException;

/**
 * Abstract validation rule
 *
 * @package  Bluz\Validator\Rule
 * @author   Anton Shevchuk
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * Message for error output
     * @var string
     */
    protected $description = 'is invalid';

    /**
     * @inheritdoc
     */
    public function assert($input): void
    {
        if (!$this->validate($input)) {
            throw new ValidatorException($this->description);
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke($input): bool
    {
        return $this->validate($input);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->getDescription();
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return __($this->description);
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description): RuleInterface
    {
        $this->description = $description;
        return $this;
    }
}
