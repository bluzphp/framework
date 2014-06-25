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
namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ValidatorException;

/**
 * AbstractRule
 *
 * @package  Bluz\Validator\Rule
 *
 * @author   Anton Shevchuk
 * @created  30.05.2014 10:10
 */
abstract class AbstractRule
{
    /**
     * Template for output
     *   - {{name}} - name of field
     *   - {{input}} - input value
     *
     * @var string
     */
    protected $template = 'Field "{{name}}" has invalid value "{{input}}"';

    /**
     * @param mixed $input
     * @return bool
     */
    abstract public function validate($input);

    /**
     * Assert
     *
     * @param string $input
     * @return bool
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException();
        }
        return true;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return __($this->template);
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTemplate();
    }
}
