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

/**
 * Check to isset module
 *
 * @param  string $module
 * @return bool
 */
use Bluz\Application\Application;

return
    function ($module = null) {
        $modulePath = Application::getInstance()->getPath() . DIRECTORY_SEPARATOR .
            'modules' . DIRECTORY_SEPARATOR . $module;

        return file_exists($modulePath);
    };
