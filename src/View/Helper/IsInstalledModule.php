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
        defined('DS') ? : define('DS', DIRECTORY_SEPARATOR);

        $modulePath = realpath($_SERVER['DOCUMENT_ROOT']) . DS .
            'application' . DS . 'modules' . DS . $module;

        return file_exists($modulePath);
    };
