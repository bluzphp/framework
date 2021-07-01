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
     * Return HTTP Status Message
     *
     * @return string
     */
    public function getStatus(): string
    {
        return StatusCode::$statusTexts[$this->code];
    }
}
