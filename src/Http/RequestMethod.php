<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http;

/**
 * RequestMethod
 *
 * @package  Bluz\Http
 * @author   dark
 */
class RequestMethod
{
    /**
     * @const string HTTP methods
     */
    public const OPTIONS = 'OPTIONS';
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const TRACE = 'TRACE';
    public const CONNECT = 'CONNECT';
}
