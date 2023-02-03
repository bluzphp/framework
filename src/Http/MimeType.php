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
    // text
    case HTML = 'text/html';
    case CSS = 'text/css';
    case JAVASCRIPT = 'text/javascript';
    // application
    case JSON = 'application/json';
    case XML = 'application/xml';
    // images
    case IMG_APNG = 'image/apng';
    case IMG_AVIF = 'image/avif';
    case IMG_GIF = 'image/gif';
    case IMG_JPEG = 'image/jpeg';
    case IMG_PNG = 'image/png';
    case IMG_SVG = 'image/svg+xml';
    case IMG_WEBP = 'image/webp';
    // multipart
    case MULTIPART_FORMDATA = 'multipart/form-data';
    case MULTIPART_BYTERANGES = 'multipart/byteranges';
    // other
    case UNKNOWN_BINARY = 'application/octet-stream';
}
