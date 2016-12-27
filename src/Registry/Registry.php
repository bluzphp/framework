<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Registry;

use Bluz\Common\Container;

/**
 * Registry
 *
 * @package  Bluz\Registry
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Registry
 */
class Registry
{
    use Container\Container;
    use Container\JsonSerialize;
    use Container\RegularAccess;
}
