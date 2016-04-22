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
namespace Bluz\Controller;

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
class Data implements \JsonSerializable
{
    use Container;
    use MagicAccess;
    use RegularAccess;
    use JsonSerialize;
}
