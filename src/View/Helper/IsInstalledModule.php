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
 * @return string|bool
 */
return
    function ($module = null) {
        $modulePath = realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR .
            'application' . DIRECTORY_SEPARATOR .
            'modules' . DIRECTORY_SEPARATOR . $module;

        return file_exists($modulePath);
    };
