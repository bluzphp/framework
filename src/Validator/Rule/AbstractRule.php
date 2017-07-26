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
    abstract public function validate($input): bool;

    /**
     * @inheritdoc
     */
    public function getDescription() : string
    {
        return __($this->description);
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
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
    public function __toString()
    {
        return $this->getDescription();
    }
}
