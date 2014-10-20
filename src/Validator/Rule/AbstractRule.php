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
use Bluz\Translator\Translator;

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
     * Template for error output
     *   - {{name}} - name of field
     *   - {{input}} - input value
     * @var string
     */
    protected $template = '{{name}} has invalid value {{input}}';

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    abstract public function validate($input);

    /**
     * Invoke
     * @param mixed $input
     * @return bool
     */
    public function __invoke($input)
    {
        return $this->validate($input);
    }

    /**
     * Assert
     * @param string $input
     * @throws ValidatorException
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
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        return Translator::translate($this->template);
    }

    /**
     * Cast to string
     * @return string
     */
    public function __toString()
    {
        return $this->getTemplate();
    }
}
