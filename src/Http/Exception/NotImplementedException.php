<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http\Exception;

use Bluz\Http\StatusCode;

/**
 * NotImplemented Exception
 *
 * @package  Bluz\Http\Exception
 * @author   Anton Shevchuk
 */
class NotImplementedException extends HttpException
{
    /**
     * @var integer HTTP code
     */
    protected $code = StatusCode::NOT_IMPLEMENTED;
}
