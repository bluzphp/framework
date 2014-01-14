<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common\Helper\Fixtures;

/**
 * Example of Class helper
 * 
 * @author   Anton Shevchuk
 * @created  14.01.14 12:27
 */
class ExampleClass
{
    /**
     * Callable
     *
     * @param mixed $argument
     * @return mixed
     */
    public function __invoke($argument)
    {
        return $argument;
    }
}

return new ExampleClass();
