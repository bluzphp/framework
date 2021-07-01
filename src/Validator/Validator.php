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
 * @method static ValidatorChain alpha($additionalCharacters = '')
 * @method static ValidatorChain alphaNumeric($additionalCharacters = '')
 * @method static ValidatorChain array($callback)
 * @method static ValidatorChain between($min, $max)
 * @method static ValidatorChain betweenInclusive($min, $max)
 * @method static ValidatorChain callback($callable, $description = null)
 * @method static ValidatorChain condition($condition)
 * @method static ValidatorChain contains($containsValue)
 * @method static ValidatorChain containsStrict($containsValue)
 * @method static ValidatorChain countryCode()
 * @method static ValidatorChain creditCard()
 * @method static ValidatorChain date($format)
 * @method static ValidatorChain domain($checkDns = false)
 * @method static ValidatorChain email($checkDns = false)
 * @method static ValidatorChain equals($compareTo)
 * @method static ValidatorChain equalsStrict($compareTo)
 * @method static ValidatorChain float()
 * @method static ValidatorChain in($haystack)
 * @method static ValidatorChain inStrict($haystack)
 * @method static ValidatorChain integer()
 * @method static ValidatorChain ip($options = null)
 * @method static ValidatorChain json()
 * @method static ValidatorChain latin($additionalCharacters = '')
 * @method static ValidatorChain latinNumeric($additionalCharacters = '')
 * @method static ValidatorChain length($min = null, $max = null)
 * @method static ValidatorChain less($maxValue)
 * @method static ValidatorChain lessOrEqual($maxValue)
 * @method static ValidatorChain more($minValue)
 * @method static ValidatorChain moreOrEqual($minValue)
 * @method static ValidatorChain notEmpty()
 * @method static ValidatorChain noWhitespace()
 * @method static ValidatorChain numeric()
 * @method static ValidatorChain required()
 * @method static ValidatorChain regexp($expression, $description = null)
 * @method static ValidatorChain slug()
 * @method static ValidatorChain string()
 */
class Validator
{
    /**
     * @var array[] list of rules namespaces
     */
    protected static $rulesNamespaces = [
        '\\Bluz\\Validator\\Rule\\'
    ];

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
     * @return ValidatorChain
     */
    public static function __callStatic($ruleName, $arguments): ValidatorChain
    {
        $validatorChain = self::create();
        return $validatorChain->$ruleName(...$arguments);
    }

    /**
     * Create new rule by name
     *
     * @param  string $ruleName
     * @param  array  $arguments
     *
     * @return RuleInterface
     * @throws Exception\ComponentException
     */
    public static function rule($ruleName, $arguments): RuleInterface
    {
        $ruleName = ucfirst($ruleName) . 'Rule';

        foreach (static::$rulesNamespaces as $ruleNamespace) {
            $ruleClass = $ruleNamespace . $ruleName;
            if (class_exists($ruleClass)) {
                return new $ruleClass(...$arguments);
            }
        }

        throw new ComponentException("Class for validator `$ruleName` not found");
    }

    /**
     * Add rules path
     *
     * @param  string $path
     *
     * @return void
     */
    public static function addRuleNamespace(string $path): void
    {
        static::$rulesNamespaces[] = rtrim($path, '\\') . '\\';
    }
}
