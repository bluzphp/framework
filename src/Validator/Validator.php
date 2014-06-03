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

/**
 * Validator
 *
 * @package  Bluz\Validator
 *
 * @method static Validator callback($callback)
 * @method static Validator notEmpty()
 * @method static Validator numeric()
 * @method static Validator regexp($expression)
 * @method static Validator string()
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
     * Magic static call for create instance of Validator
     *
     * @param string $ruleName
     * @param array $arguments
     * @return Validator
     */
    public static function __callStatic($ruleName, $arguments)
    {
        $validator = new static;

        return $validator->__call($ruleName, $arguments);
    }

    /**
     * Magic call for create new rule
     *
     * @param string $ruleName
     * @param array $arguments
     * @return Validator
     */
    public function __call($ruleName, $arguments)
    {
        $ruleClass = '\\Bluz\\Validator\\Rule\\' . ucfirst($ruleName);

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
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
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
        $this->invalid = array();
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
     * Validate by array key or object property
     *
     * @param array|object $input
     * @param string $key
     * @param bool $all
     * @return bool
     */
    public function validateProperty($input, $key, $all = false)
    {
        if (is_array($input) && isset($input[$key])) {
            $this->input = $input[$key];
        } elseif (is_object($input) && isset($input->{$key})) {
            $this->input = $input->{$key};
        }
        return $this->validate($this->input, $all);
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
     * @return bool|string
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
        return sprintf($message, $this->getName(), $this->getInput());
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
