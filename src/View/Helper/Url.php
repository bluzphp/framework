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
use Bluz\View\View;
use Bluz\View\ViewException;

return
    /**
     * @param string $module
     * @param string $controller
     * @param array $params
     * @param bool $checkAccess
     * @return string|null
     */
    function ($module, $controller, array $params = [], $checkAccess = false) {
    /** @var View $this */
    $app = app();

    try {
        if ($checkAccess) {
            $controllerFile = $app->getControllerFile($module, $controller);
            $reflectionData = $app->reflection($controllerFile);
            if (!$app->isAllowed($module, $reflectionData)) {
                return null;
            }
        }
    } catch (\Exception $e) {
        throw new ViewException('Url View Helper: ' . $e->getMessage());
    }

    if (null === $module) {
        $module = $app->getRequest()->getModule();
    }
    if (null === $controller) {
        $controller = $app->getRequest()->getController();
    }
    if (null === $params) {
        $params = $app->getRequest()->getParams();
    }

    return $app->getRouter()
        ->url($module, $controller, $params);
    };
