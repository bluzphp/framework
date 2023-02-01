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
 * NotFound Exception
 *
 * @package  Bluz\Http\Exception
 * @author   Anton Shevchuk
 */
class NotFoundException extends HttpException
{
    protected StatusCode $statusCode = StatusCode::NOT_FOUND;
}
