<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Http;

/**
 * HTTP Cache
 *
 * Wrapper for working with HTTP headers
 *     - Cache-Control
 *     - Expires
 *     - ETag
 *     - Last-Modified
 *
 * @package  Bluz\Http
 *
 * @link     http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html
 * @link     http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 *
 * @author   Anton Shevchuk
 * @created  03.11.2014 13:14
 */
class Cache
{
    /**
     * @var Response
     */
    protected $response;
}
