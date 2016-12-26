<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
