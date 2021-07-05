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
 * @param string|null $title
 *
 * @return string
 */
return
    function (?string $title = null) {
        // it's stack for <title> tag
        if (null === $title) {
            return Registry::get('layout:title');
        }

        Registry::set('layout:title', $title);
        return $title;
    };
