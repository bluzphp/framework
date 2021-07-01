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
use Bluz\Http\Exception\ForbiddenException;
use Bluz\View\View;

/**
 * Dispatch controller View Helper
 *
 * Example of usage:
 *     $this->dispatch($module, $controller, array $params);
 *
 * @param  string $module
 * @param  string $controller
 * @param  array  $params
 *
 * @return View|string|null
 */
return
    function ($module, $controller, $params = []) {
        /**
         * @var View $this
         */
        try {
            $view = Application::getInstance()->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            // nothing for ForbiddenException
            return null;
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        // run closure
        return value($view);
    };
