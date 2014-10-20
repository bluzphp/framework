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
namespace Bluz\Application\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Router;

return
    /**
     * Redirect to controller
     * @var Application $this
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return void
     */
    function ($module = 'index', $controller = 'index', $params = array()) {
        $url = Router::getUrl($module, $controller, $params);
        $this->redirect($url);
    };
