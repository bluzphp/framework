<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Container\ArrayAccess;
use Bluz\Container\Container;
use Bluz\Container\JsonSerialize;
use Bluz\Container\MagicAccess;
use Bluz\Container\RegularAccess;

/**
 * Data
 *
 * @package  Bluz\Controller
 * @author   Anton Shevchuk
 */
class Data implements \JsonSerializable, \ArrayAccess
{
    use ArrayAccess;
    use Container;
    use MagicAccess;
    use RegularAccess;
    use JsonSerialize;
}
