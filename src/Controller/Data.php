<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Common\Container\ArrayAccess;
use Bluz\Common\Container\Container;
use Bluz\Common\Container\JsonSerialize;
use Bluz\Common\Container\MagicAccess;
use Bluz\Common\Container\RegularAccess;

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
