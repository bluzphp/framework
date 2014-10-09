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
use Bluz\Proxy\Request;
use Bluz\Proxy\Router;
use Bluz\View\View;
use Bluz\View\ViewException;

return
    /**
     * Generate URL
     *
     * @var View $this
     * @param string $module
     * @param string $controller
     * @param array $params
     * @param bool $checkAccess
     * @return string|null
     */
    function ($module, $controller, $params = [], $checkAccess = false) {
        try {
            if ($checkAccess) {
                if (!Application::getInstance()->isAllowed($module, $controller)) {
                    return null;
                }
            }
        } catch (\Exception $e) {
            throw new ViewException('Url View Helper: ' . $e->getMessage());
        }

        if (null === $module) {
            $module = Request::getModule();
        }
        if (null === $controller) {
            $controller = Request::getController();
        }
        if (null === $params) {
            $params = Request::getParams();
        }

        return Router::getUrl($module, $controller, $params);
    };
