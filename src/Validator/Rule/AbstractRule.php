<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Translator\Translator;
use Bluz\Validator\Exception\ValidatorException;

/**
 * Abstract validation rule
 *
 * @package  Bluz\Validator\Rule
 * @author   Anton Shevchuk
 */
abstract class AbstractRule
{
    /**
     * Template for error output
     *   - {{name}} - name of field
     *   - {{input}} - input value
     *
     * @var string
     */
    protected $template = '{{name}} has invalid value {{input}}';

    /**
     * Check input data
     *
     * @param  mixed $input
     * @return bool
     */
    abstract public function validate($input) : bool;

    /**
     * Invoke
     *
     * @param  mixed $input
     * @return bool
     */
    public function __invoke($input) : bool
    {
        return $this->validate($input);
    }

    /**
     * Assert
     *
     * @param  string $input
     * @return bool
     * @throws ValidatorException
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException();
        }
        return true;
    }

    /**
     * Set error template
     *
     * @param  string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        return Translator::translate($this->template);
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTemplate();
    }
}
