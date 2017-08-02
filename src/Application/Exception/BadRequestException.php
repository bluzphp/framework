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
 * BadRequest Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class BadRequestException extends ApplicationException
{
    /**
     * @var integer HTTP Code
     */
    protected $code = StatusCode::BAD_REQUEST;

    /**
     * @var string
     */
    protected $message = 'Bad Request';
}
