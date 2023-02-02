<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Common;

use Bluz\Container\ArrayAccess;
use Bluz\Container\Container;
use Bluz\Container\JsonSerialize;
use Bluz\Container\MagicAccess;
use Bluz\Container\RegularAccess;

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
