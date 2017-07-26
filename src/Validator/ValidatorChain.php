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
 * @method ValidatorChain callback($callback)
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
 * @method ValidatorChain regexp($expression)
 * @method ValidatorChain slug()
 * @method ValidatorChain string()
 */
class ValidatorChain
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
     * addRule
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
                $this->error = $rule->getDescription();
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
     * Get required flag
     *
     * @return bool
     */
    public function isRequired() : bool
    {
        return array_key_exists('required', $this->rules);
    }

    /**
     * Get error message
     *
     * @return null|string
     */
    public function getError()
    {
        if ($this->description) {
            return $this->description;
        }
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
        if ($this->description) {
            return [$this->description];
        }

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

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return implode("\n", $this->getDescription());
    }
}
