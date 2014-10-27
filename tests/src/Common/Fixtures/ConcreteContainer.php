<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common\Fixtures;

use Bluz\Common\Container\ArrayAccess;
use Bluz\Common\Container\Container;
use Bluz\Common\Container\JsonSerialize;
use Bluz\Common\Container\MagicAccess;
use Bluz\Common\Container\RegularAccess;

/**
 * Concrete class with Container trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  27.10.14 12:27
 */
class ConcreteContainer implements \ArrayAccess, \JsonSerializable
{
    use Container;
    use ArrayAccess;
    use MagicAccess;
    use RegularAccess;
    use JsonSerialize;
}
 