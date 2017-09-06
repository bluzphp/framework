<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Exception;

use Bluz\Application\Exception\BadRequestException;

/**
 * Validator Exception
 *
 * @package  Bluz\Validator\Exception
 * @author   Anton Shevchuk
 */
class ValidatorException extends BadRequestException
{
    /**
     * @var string exception message
     */
    protected $message = 'Invalid Arguments';
}
