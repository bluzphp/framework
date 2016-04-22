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
use Bluz\Proxy\Layout;

return
    /**
     * Switch layout
     *
     * @return void
     */
    function ($layout) {
        Application::getInstance()->useLayout(true);
        Layout::setTemplate($layout);
    };
