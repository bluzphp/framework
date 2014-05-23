<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common\Fixtures;

use Bluz\Common\Options;

/**
 * Concrete class with Options trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  14.01.14 11:48
 */
class ConcreteOptions
{
    use Options;

    /**
     * @var mixed
     */
    public $foo;

    /**
     * @var mixed
     */
    public $fooBar;

    /**
     * setFoo
     *
     * @param string $foo
     * @return self
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
        return $this;
    }

    /**
     * setFooBar
     *
     * @param string $fooBar
     * @return self
     */
    public function setFooBar($fooBar)
    {
        $this->fooBar = $fooBar;
        return $this;
    }
}
