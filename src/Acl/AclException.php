<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Acl;

use Bluz\Application\Exception\ForbiddenException;

/**
 * Acl Exception
 *
 * @package  Bluz\Acl
 * @author   Anton Shevchuk
 */
class AclException extends ForbiddenException
{
}
