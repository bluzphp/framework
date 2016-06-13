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

use Bluz\Application\Application;

/**
 * Return Exception message
 *
 * @param  \Exception $exception
 * @return string
 */
return
    function ($exception) {
        /**
         * @var \Exception $exception
         */
        if (Application::getInstance()->isDebug()) {
            // @codeCoverageIgnoreStart
            // exception message for developers
            return
                '<div class="alert alert-error">' .
                '<strong>Exception</strong>: ' .
                $exception->getMessage() .
                '</div>';
            // @codeCoverageIgnoreEnd
        } else {
            return '';
        }
    };
