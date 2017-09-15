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
use Bluz\Validator\Rule\RuleInterface;

/**
 * Chain of Validators
 *
 * Chain can consists one or more validation rules
 *
 * @package  Bluz\Validator
 * @author   Anton Shevchuk
 *
 * @method ValidatorChain alpha($additionalCharacters = '')
 * @method ValidatorChain alphaNumeric($additionalCharacters = '')
 * @method ValidatorChain array($callback)
 * @method ValidatorChain between($min, $max)
 * @method ValidatorChain betweenInclusive($min, $max)
 * @method ValidatorChain condition($condition)
 * @method ValidatorChain contains($containsValue)
 * @method ValidatorChain containsStrict($containsValue)
 * @method ValidatorChain countryCode()
 * @method ValidatorChain creditCard()
 * @method ValidatorChain date($format)
 * @method ValidatorChain domain($checkDns = false)
 * @method ValidatorChain email($checkDns = false)
 * @method ValidatorChain equals($compareTo)
 * @method ValidatorChain equalsStrict($compareTo)
 * @method ValidatorChain float()
 * @method ValidatorChain in($haystack)
 * @method ValidatorChain inStrict($haystack)
 * @method ValidatorChain integer()
 * @method ValidatorChain ip($options = null)
 * @method ValidatorChain json()
 * @method ValidatorChain latin($additionalCharacters = '')
 * @method ValidatorChain latinNumeric($additionalCharacters = '')
 * @method ValidatorChain length($min = null, $max = null)
 * @method ValidatorChain less($maxValue)
 * @method ValidatorChain lessOrEqual($maxValue)
 * @method ValidatorChain more($minValue)
 * @method ValidatorChain moreOrEqual($minValue)
 * @method ValidatorChain notEmpty()
 * @method ValidatorChain noWhitespace()
 * @method ValidatorChain numeric()
 * @method ValidatorChain required()
 * @method ValidatorChain slug()
 * @method ValidatorChain string()
 */
class ValidatorChain implements ValidatorInterface
{
    /**
     * @var string description of rules chain
     */
    protected $description;

    /**
     * @var RuleInterface[] list of validation rules
     */
    protected $rules = [];

    /**
     * @var string Error message
     */
    protected $error;

    /**
     * Magic call for create new rule
     *
     * @param  string $ruleName
     * @param  array  $arguments
     *
     * @return ValidatorChain
     * @throws Exception\ComponentException
     */
    public function __call($ruleName, $arguments) : ValidatorChain
    {
        $this->addRule(Validator::rule($ruleName, $arguments));
        return $this;
    }

    /**
     * Add Rule to ValidatorChain
     *
     * @param RuleInterface $rule
     *
     * @return ValidatorChain
     */
    public function addRule($rule) : ValidatorChain
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Get required flag
     *
     * @return bool
     */
    public function isRequired() : bool
    {
        return array_key_exists('required', $this->rules);
    }

    /**
     * Add Callback Rule to ValidatorChain
     *
     * @param mixed       $callable
     * @param string|null $description
     *
     * @return ValidatorChain
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function callback($callable, $description = null) : ValidatorChain
    {
        $rule = Validator::rule('callback', [$callable]);
        if (null !== $description) {
            $rule->setDescription($description);
        }
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add Regexp Rule to ValidatorChain
     *
     * @param string      $expression
     * @param string|null $description
     *
     * @return ValidatorChain
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function regexp($expression, $description = null) : ValidatorChain
    {
        $rule = Validator::rule('regexp', [$expression]);
        if (null !== $description) {
            $rule->setDescription($description);
        }
        $this->addRule($rule);
        return $this;
    }

    /**
     * Validate chain of rules
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input) : bool
    {
        $this->error = null; // clean
        foreach ($this->rules as $rule) {
            if (!$rule->validate($input)) {
                // apply custom description or use description from rule
                $this->setError($this->description ?? $rule->getDescription());
                return false;
            }
        }
        return true;
    }

    /**
     * Assert
     *
     * @param  mixed $input
     *
     * @throws ValidatorException
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException($this->getError());
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke($input) : bool
    {
        return $this->validate($input);
    }

    /**
     * @inheritdoc
     */
    public function __toString() : string
    {
        return implode("\n", $this->getDescription());
    }

    /**
     * Get error message
     *
     * @return null|string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set error message for replace text from rule
     *
     * @param  string $message
     *
     * @return ValidatorChain
     */
    protected function setError($message) : ValidatorChain
    {
        $this->error = $message;
        return $this;
    }

    /**
     * Get validation description
     *
     * @return array
     */
    public function getDescription() : array
    {
        // apply custom description
        if ($this->description) {
            return [$this->description];
        }

        // eject description from rules
        return array_map('strval', $this->rules);
    }

    /**
     * Set validation description
     *
     * @param  string $message
     *
     * @return ValidatorChain
     */
    public function setDescription($message) : ValidatorChain
    {
        $this->description = $message;
        return $this;
    }
}
