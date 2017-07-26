<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator;

use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Rule\RuleInterface;

/**
 * Validator
 *
 * @package  Bluz\Validator
 * @author   Anton Shevchuk
 * @link     https://github.com/Respect/Validation
 *
 * @method static RuleInterface alpha($additionalCharacters = '')
 * @method static RuleInterface alphaNumeric($additionalCharacters = '')
 * @method static RuleInterface array($callback)
 * @method static RuleInterface between($min, $max)
 * @method static RuleInterface betweenInclusive($min, $max)
 * @method static RuleInterface callback($callback)
 * @method static RuleInterface condition($condition)
 * @method static RuleInterface contains($containsValue)
 * @method static RuleInterface containsStrict($containsValue)
 * @method static RuleInterface countryCode()
 * @method static RuleInterface creditCard()
 * @method static RuleInterface date($format)
 * @method static RuleInterface domain($checkDns = false)
 * @method static RuleInterface email($checkDns = false)
 * @method static RuleInterface equals($compareTo)
 * @method static RuleInterface equalsStrict($compareTo)
 * @method static RuleInterface float()
 * @method static RuleInterface in($haystack)
 * @method static RuleInterface inStrict($haystack)
 * @method static RuleInterface integer()
 * @method static RuleInterface ip($options = null)
 * @method static RuleInterface json()
 * @method static RuleInterface latin($additionalCharacters = '')
 * @method static RuleInterface latinNumeric($additionalCharacters = '')
 * @method static RuleInterface length($min = null, $max = null)
 * @method static RuleInterface less($maxValue)
 * @method static RuleInterface lessOrEqual($maxValue)
 * @method static RuleInterface more($minValue)
 * @method static RuleInterface moreOrEqual($minValue)
 * @method static RuleInterface notEmpty()
 * @method static RuleInterface noWhitespace()
 * @method static RuleInterface numeric()
 * @method static RuleInterface required()
 * @method static RuleInterface regexp($expression)
 * @method static RuleInterface slug()
 * @method static RuleInterface string()
 */
class Validator
{
    /**
     * Create new instance if ValidatorChain
     *
     * @return ValidatorChain
     */
    public static function create(): ValidatorChain
    {
        return new ValidatorChain();
    }

    /**
     * Magic static call for create instance of Validator
     *
     * @param string $ruleName
     * @param array  $arguments
     *
     * @return RuleInterface
     * @throws Exception\ComponentException
     */
    public static function __callStatic($ruleName, $arguments)
    {
        return self::rule($ruleName, $arguments);
    }

    /**
     * Create new rule by name
     *
     * @todo   create extension point for custom rules
     *
     * @param  string $ruleName
     * @param  array  $arguments
     *
     * @return RuleInterface
     * @throws Exception\ComponentException
     */
    public static function rule($ruleName, $arguments) : RuleInterface
    {
        $ruleClass = '\\Bluz\\Validator\\Rule\\' . ucfirst($ruleName) . 'Rule';

        if (!class_exists($ruleClass)) {
            throw new ComponentException("Class for validator `$ruleName` not found");
        }

        return new $ruleClass(...$arguments);
    }
}
