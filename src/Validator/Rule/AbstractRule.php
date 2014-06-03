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
     *   - %1$s - name
     *   - %2$s - input value
     *
     * @var string
     */
    protected $template = 'Field "%1$s" has invalid value "%2$s"';

    /**
     * @param mixed $input
     * @return bool
     */
    abstract public function validate($input);

    /**
     * Set custom template for error message
     *
     * @param string $template
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return __($this->template);
    }
}
 