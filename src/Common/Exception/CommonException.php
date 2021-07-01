<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common\Exception;

use Bluz\Http\StatusCode;

/**
 * Basic Exception for Bluz framework
 *
 * @package  Bluz\Common\Exception
 * @author   Anton Shevchuk
 */
class CommonException extends \Exception
{
    /**
     * @var integer Used as default HTTP code for exceptions
     */
    protected $code = StatusCode::INTERNAL_SERVER_ERROR;
}
