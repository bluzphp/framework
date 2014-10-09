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

return
    /**
     * API call
     *
     * Be carefully, use it for calculate/update/save some data
     * For render information use Widgets!
     *     $this->api($module, $method, array $params);
     *
     * @var View $this
     * @param string $module
     * @param string $method
     * @param array $params
     * @return mixed
     */
    function ($module, $method, $params = array()) {
        try {
            $apiClosure = Application::getInstance()->api($module, $method);
            return call_user_func_array($apiClosure, $params);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    };
