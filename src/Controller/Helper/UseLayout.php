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
use Bluz\Controller\Controller;
use Bluz\Proxy\Layout;

/**
 * Switch layout
 *
 * @param $layout
 */
return
    function ($layout) {
        /**
         * @var Controller $this
         */
        Application::getInstance()->useLayout(true);
        Layout::setTemplate($layout);
    };
