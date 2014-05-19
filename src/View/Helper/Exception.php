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

use Bluz\View\View;

return
    /**
     * Return Exception message
     *
     * @var View $this
     * @param \Exception $exception
     * @return string
     */
    function ($exception) {
        if (app()->isDebug()) {
            // exception message for developers
            return
                '<div class="alert alert-error">' .
                '<strong>Exception</strong>: ' .
                $exception->getMessage() .
                '</div>';
        } else {
            return '';
        }
    };
