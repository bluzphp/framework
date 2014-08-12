<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common\Fixtures;

use Bluz\Common\Singleton;

/**
 * Concrete class with Singleton trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 13:24
 */
class ConcreteSingleton
{
    use Singleton;

    /**
     * @var string
     */
    public $foo;
}
