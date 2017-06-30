<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

/**
 * Generate HTML for <style> or <link> element
 *
 * @param  string $code
 * @param  string $media
 *
 * @return string
 */
return
    function ($code, $media = 'all') {
        return "<style type=\"text/css\" media=\"$media\">\n$code\n</style>\n";
    };
