<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http\Exception;

use Bluz\Common\Exception\CommonException;
use Bluz\Http\StatusCode;

/**
 * HttpException
 *
 * @package  Bluz\Http\Exception
 * @author   Anton Shevchuk
 */
class HttpException extends CommonException
{
    /**
     * @var StatusCode Used as default HTTP code for exceptions
     */
    protected StatusCode $statusCode = StatusCode::INTERNAL_SERVER_ERROR;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?: $this->statusCode->message(),
            $code ?: $this->statusCode->value,
            $previous
        );
    }

    /**
     * Return HTTP Status Message
     *
     * @return StatusCode
     */
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }

    /**
     * Return HTTP Status Message
     *
     * @return string
     */
    public function getStatusCodeMessage(): string
    {
        return $this->statusCode->message();
    }
}
