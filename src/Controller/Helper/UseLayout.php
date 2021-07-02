<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Application\Application;
use Bluz\Controller\Controller;
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
        Application::getInstance()->useLayout(true);
        Layout::setTemplate($layout);
    };
