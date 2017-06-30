<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Application\Exception;

use Bluz\Http\StatusCode;

/**
 * Not Allowed Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotAllowedException extends ApplicationException
{
    /**
     * @var integer HTTP Code
     */
    protected $code = StatusCode::METHOD_NOT_ALLOWED;
}
