<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Proxy\Application;
use Bluz\Proxy\Layout;
use Bluz\View\View;

/**
 * Set or generate <script> code for <head>
 *
 * @param string|null $src
 * @param array $attributes
 *
 * @return null|string
 */
return
    function (?string $src = null, array $attributes = []) {
        /**
         * @var View $this
         */
        if (Application::getInstance()->hasLayout()) {
            return Layout::headScript($src, $attributes);
        }
        // it's just alias to script() call
        return $this->script($src, $attributes);
    };
