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
 * MimeType
 *
 * @package  Bluz\Http
 * @author   Anton Shevchuk
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
 */
enum MimeType: string
{
    case ANY = '*/*';
    case CSS = 'text/css';
    case HTML = 'text/html';
    case JAVASCRIPT = 'text/javascript';
    case JSON = 'application/json';
    case XML = 'application/xml';
}
