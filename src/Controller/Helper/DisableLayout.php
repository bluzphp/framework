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

return
    /**
     * Switch layout or disable it
     *
     * @return void
     */
    function () {
        Application::getInstance()->useLayout(false);
    };
