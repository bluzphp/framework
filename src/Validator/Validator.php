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

/**
 * Validator
 *
 * @package  Bluz\Validator
 *
 * @method static Validator alpha()
 * @method static Validator alphaNumeric()
 * @method static Validator callback($callback)
 * @method static Validator integer()
 * @method static Validator length($min = null, $max = null, $inclusive = true)
 * @method static Validator max($maxValue, $inclusive = false)
 * @method static Validator min($minValue, $inclusive = false)
 * @method static Validator notEmpty()
 * @method static Validator noWhitespace()
 * @method static Validator numeric()
 * @method static Validator required()
 * @method static Validator regexp($expression)
 * @method static Validator string()
 *
 * @see https://github.com/Respect/Validation
 *
 * @author   Anton Shevchuk
 * @created  30.05.2014 10:03
 */
class Validator
{
    /**
     * @var AbstractRule[]
     */
    protected $rules = array();

    /**
     * @var AbstractRule[]
     */
    protected $invalid = array();

    /**
     * @var string of field name
     */
    protected $name;

    /**
     * @var string of input data
     */
    protected $input;

    /**
     * @var string rule as text
     */
    protected $error;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * Create new instance if Validator
     *
     * @return Validator
     */
    public static function create()
    {
        return new static;
    }

    /**
     * Magic static call for create instance of Validator
     *
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
     *
     * @param string $ruleName
     * @param array $arguments
     * @throws Exception\ComponentException
     * @return Validator
     */
    public function __call($ruleName, $arguments)
    {
        $ruleClass = '\\Bluz\\Validator\\Rule\\' . ucfirst($ruleName);

        if (!class_exists($ruleClass)) {
            throw new ComponentException();
        }

        if ($ruleName == 'required') {
            $this->required = true;
        }

        if (sizeof($arguments)) {
            $reflection = new \ReflectionClass($ruleClass);
            $rule = $reflection->newInstanceArgs($arguments);
        } else {
            $rule = new $ruleClass($arguments);
        }

        $this->addRule($rule);

        return $this;
    }

    /**
     * Get required flag
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Add rule to validator
     *
     * @param AbstractRule $rule
     * @return Validator
     */
    public function addRule($rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Get all rules
     *
     * @return AbstractRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get Invalid Rules
     *
     * @return AbstractRule[]
     */
    public function getInvalidRules()
    {
        return $this->invalid;
    }

    /**
     * Set field Title
     *
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
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get input data
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Callable
     *
     * @param mixed $input
     * @return bool
     */
    public function __invoke($input)
    {
        return $this->validate($input);
    }

    /**
     * Validate chain of rules
     *
     * @param mixed $input
     * @param bool $all
     * @return bool
     */
    public function validate($input, $all = false)
    {
        $this->input = $input;
        $this->invalid = array(); // clean
        foreach ($this->getRules() as $rule) {
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
     *
     * @param mixed $input
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
     *
     * @param string $message
     * @throws \Bluz\Common\Exception
     * @return Validator
     */
    public function setError($message)
    {
        $this->error = $message;
        return $this;
    }

    /**
     * Get error message
     *
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
     * Prepare error message for output
     *
     * @param string $message
     * @return string
     */
    protected function prepareError($message)
    {
        $message = str_replace('{{name}}', $this->getName(), $message);
        $message = str_replace('{{input}}', esc($this->getInput()), $message);

        return $message;
    }

    /**
     * getErrors
     *
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
     * __toString
     *
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
