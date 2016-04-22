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
namespace Bluz\Controller\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Response;

return
    /**
     * Switch to JSON content
     *
     * @return void
     */
    function () {
        Application::getInstance()->useLayout(false);
        Response::setHeader('Content-Type', 'application/json');
    };
