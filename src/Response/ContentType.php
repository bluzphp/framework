<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Response;

/**
 * Response types
 *
 * @package  Bluz\Response
 */
enum ContentType: string
{
    case ANY = 'ANY';
    case CLI = 'CLI';
    case HTML = 'HTML';
    case JSON = 'JSON';
    case FILE = 'FILE';
}
