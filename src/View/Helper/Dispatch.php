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
use Bluz\Application\Exception\ForbiddenException;
use Bluz\View\View;

return
    /**
     * Dispatch controller View Helper
     *
     * Example of usage:
     *     $this->dispatch($module, $controller, array $params);
     *
     * @var View $this
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return View|string|null
     */
    function ($module, $controller, $params = array()) {
        try {
            $view = Application::getInstance()->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            // nothing for ForbiddenException
            return null;
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        // run closure
        if ($view instanceof \Closure) {
            return $view();
        }
        return $view;
    };
