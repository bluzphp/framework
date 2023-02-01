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
use Bluz\Proxy\Response;
use Bluz\Response\ContentType;

/**
 * Switch to JSON content
 *
 * @return void
 */
return
    function () {
        /**
         * @var Controller $this
         */
        Application::getInstance()->useLayout(false);
        Response::setContentType(ContentType::JSON);
    };
