<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
        if ($exception && Application::getInstance()->isDebug()) {
            // @codeCoverageIgnoreStart
            // exception message for developers
            return
                '<div class="alert alert-warning">' .
                '<strong>Exception</strong>' .
                '<p>'. esc($exception->getMessage()) .'</p>'.
                '</div>';
            // @codeCoverageIgnoreEnd
        }
        return '';
    };
