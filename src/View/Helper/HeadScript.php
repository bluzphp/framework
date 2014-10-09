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
namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Layout;

return
    /**
     * Set or generate <script> code for <head>
     *
     * @var Layout $this
     * @param string $script
     * @return string|null
     */
    function ($script = null) {
        if (Application::getInstance()->hasLayout()) {
            return Layout::headScript($script);
        } else {
            // it's just alias to script() call
            return $this->script($script);
        }
    };
