<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

/**
 * Set or generate <title> code for <head>
 *
 * @param  string $title
 * @param  string $separator
 *
 * @return string
 */
return
    function ($title, $separator = ' :: ') {
        // it's stack for <title> tag
        $oldTitle = Registry::get('layout:title');
        $result = (!$oldTitle ?: $oldTitle . $separator) . $title;
        Registry::set('layout:title', $result);
        return $result;
    };
