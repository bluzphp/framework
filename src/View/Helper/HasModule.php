<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Application\Application;

/**
 * Check to isset module
 *
 * @param  string $module
 *
 * @return bool
 */
return
    function ($module = null) {
        $modulePath = Application::getInstance()->getPath() . DIRECTORY_SEPARATOR .
            'modules' . DIRECTORY_SEPARATOR . $module;
        return file_exists($modulePath);
    };
