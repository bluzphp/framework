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
 * NotImplemented Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotImplementedException extends ApplicationException
{
    /**
     * @var integer HTTP code
     */
    protected $code = StatusCode::NOT_IMPLEMENTED;

    /**
     * @var string
     */
    protected $message = 'Not Implemented';
}
