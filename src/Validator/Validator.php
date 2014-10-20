<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Validator;

use Bluz\Validator\Rule\AbstractRule;
use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\Rule\Required;

/**
 * Validator
 *
 * @package  Bluz\Validator
 *
 * @method static Validator alpha($additionalCharacters = '')
 * @method static Validator alphaNumeric($additionalCharacters = '')
 * @method static Validator arrayInput($callback)
 * @method static Validator between($min, $max, $inclusive = false)
 * @method static Validator callback($callback)
 * @method static Validator condition($condition)
 * @method static Validator contains($containsValue, $identical = false)
 * @method static Validator countryCode()
 * @method static Validator creditCard()
 * @method static Validator date($format)
 * @method static Validator domain($checkDns = false)
 * @method static Validator email($checkDns = false)
 * @method static Validator equals($compareTo, $identical = false)
 * @method static Validator float()
 * @method static Validator in($haystack, $identical = false)
 * @method static Validator integer()
 * @method static Validator ip($options = null)
 * @method static Validator json()
 * @method static Validator latin($additionalCharacters = '')
 * @method static Validator latinNumeric($additionalCharacters = '')
 * @method static Validator length($min = null, $max = null, $inclusive = true)
 * @method static Validator max($maxValue, $inclusive = false)
 * @method static Validator min($minValue, $inclusive = false)
 * @method static Validator notEmpty()
 * @method static Validator noWhitespace()
 * @method static Validator numeric()
 * @method static Validator required()
 * @method static Validator regexp($expression)
 * @method static Validator slug()
 * @method static Validator string()
 *
 * @link https://github.com/Respect/Validation
 *
 * @author   Anton Shevchuk
 * @created  30.05.2014 10:03
 */
class Validator
{
    /**
     * @var AbstractRule[] Stack of validation rules
     */
    protected $rules = array();

    /**
     * @var AbstractRule[] Stack of invalid rules
     */
    protected $invalid = array();

    /**
     * @var string Field name
     */
    protected $name;

    /**
     * @var string Input data
     */
    protected $input;

    /**
     * @var string Error text
     */
    protected $error;

    /**
     * Create new instance if Validator
     * @return Validator
     */
    public static function create()
    {
        return new static;
    }

    /**
     * Magic static call for create instance of Validator
     * @param string $ruleName
     * @param array $arguments
     * @return Validator
     */
    public static function __callStatic($ruleName, $arguments)
    {
        $validator = self::create();

        return $validator->__call($ruleName, $arguments);
    }

    /**
     * Magic call for create new rule
     * @param string $ruleName
     * @param array $arguments
     * @throws Exception\ComponentException
     * @return Validator
     */
    public function __call($ruleName, $arguments)
    {
        $ruleClass = '\\Bluz\\Validator\\Rule\\' . ucfirst($ruleName);

        if (!class_exists($ruleClass)) {
            throw new ComponentException("Class for validator `$ruleName` not found");
        }

        if (sizeof($arguments)) {
            $reflection = new \ReflectionClass($ruleClass);
            $rule = $reflection->newInstanceArgs($arguments);
        } else {
            $rule = new $ruleClass();
        }

        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Get required flag
     * @return bool
     */
    public function isRequired()
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof Required) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set field Title
     * @param string $name
     * @return Validator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get field Title
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get input data
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Callable
     * @param mixed $input
     * @return bool
     */
    public function __invoke($input)
    {
        return $this->validate($input);
    }

    /**
     * Validate chain of rules
     * @param mixed $input
     * @param bool $all
     * @return bool
     */
    public function validate($input, $all = false)
    {
        $this->input = $input;
        $this->invalid = array(); // clean
        foreach ($this->rules as $rule) {

            if (!$rule->validate($this->input)) {
                $this->invalid[] = $rule;
                if (!$all) {
                    break;
                }
            }
        }
        return sizeof($this->invalid) ? false : true;
    }

    /**
     * Assert
     * @param mixed $input
     * @throws ValidatorException
     * @return bool
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException($this->getError()?:'');
        }
        return true;
    }

    /**
     * Set error template for complex rule
     * @param string $message
     * @return Validator
     */
    public function setError($message)
    {
        $this->error = $message;
        return $this;
    }

    /**
     * Get error message
     * @return false|string
     */
    public function getError()
    {
        // nothing for valid
        if (!sizeof($this->invalid)) {
            return false;
        }

        // one custom message for all rules in chain
        // or predefined message from last rule in chain
        if ($this->error) {
            $output = $this->error;
        } else {
            $output = end($this->invalid);
        }
        return $this->prepareError($output);
    }

    /**
     * getErrors
     * @return string[]
     */
    public function getErrors()
    {
        $output = array();
        foreach ($this->invalid as $rule) {
            $output[] = $this->prepareError($rule);
        }
        return $output;
    }

    /**
     * Prepare error message for output
     * @param string $message
     * @return string
     */
    protected function prepareError($message)
    {
        $input = $this->getInput();
        if (is_array($input)) {
            $input = join(', ', $input);
        }

        $message = str_replace('{{name}}', $this->getName(), $message);
        $message = str_replace('{{input}}', esc($input), $message);

        return $message;
    }

    /**
     * Cast to string
     * @return string
     */
    public function __toString()
    {
        // custom message for all chain of rules
        if ($this->error) {
            $output = $this->error;
        } else {
            $output = join("\n", $this->rules);
        }
        return $this->prepareError($output);
    }
}
