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

/**
 * Switch layout or disable it
 *
 * @return void
 */
return
    function () {
        /**
         * @var Controller $this
         */
        Application::getInstance()->disableLayout();
    };
