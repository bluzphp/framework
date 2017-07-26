<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Exception;

use Bluz\Common\Container\ArrayAccess;
use Bluz\Common\Container\Container;

/**
 * Exception of Validator Chain
 *
 * @package  Bluz\Validator\Exception
 * @author   Anton Shevchuk
 */
class ValidatorFormException extends ValidatorException implements \ArrayAccess
{
    use Container;
    use ArrayAccess;
}
