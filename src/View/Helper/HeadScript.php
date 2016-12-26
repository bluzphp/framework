<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Layout;
use Bluz\View\View;

/**
 * Set or generate <script> code for <head>
 *
 * @param  string $script
 * @return string|null
 */
return
    function ($script = null) {
        /**
         * @var View $this
         */
        if (Application::getInstance()->useLayout()) {
            return Layout::headScript($script);
        } else {
            // it's just alias to script() call
            return $this->script($script);
        }
    };
