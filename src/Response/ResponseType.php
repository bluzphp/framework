<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Response;

use Bluz\Http\MimeType;

/**
 * Response types
 *
 * @package  Bluz\Response
 */
enum ResponseType: string
{
    case ANY = 'ANY';
    case CLI = 'CLI';
    case HTML = 'HTML';
    case JSON = 'JSON';
    case FILE = 'FILE';

    public function getMimeType(): MimeType
    {
        return match ($this) {
            self::ANY, self::CLI, self::FILE => MimeType::ANY,
            self::HTML => MimeType::HTML,
            self::JSON => MimeType::JSON,
        };
    }
}
