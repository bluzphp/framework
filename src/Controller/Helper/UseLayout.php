<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Controller\Controller;
use Bluz\Proxy\Application;
use Bluz\Proxy\Layout;

/**
 * Switch layout
 *
 * @param string $layout
 */
return
    function (string $layout) {
        /**
         * @var Controller $this
         */
        Application::getInstance()->enableLayout();
        Layout::setTemplate($layout);
    };
