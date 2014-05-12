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
     * API call View Helper
     *
     * Be carefully, use it for calculate/update/save some data
     * For render information use Widgets!
     *     $this->api($module, $method, array $params);
     *
     * @param string $module
     * @param string $method
     * @param array $params
     * @return mixed
     */
    function ($module, $method, $params = array()) {
    /** @var View $this */
    $application = app();
    try {
        $apiClosure = $application->api($module, $method);
        return call_user_func_array($apiClosure, $params);
    } catch (\Exception $e) {
        if (app()->isDebug()) {
            // exception message for developers
            echo
                '<div class="alert alert-error">' .
                '<strong>API "' . $module . '/' . $method . '"</strong>: ' .
                $e->getMessage() .
                '</div>';
        }
        return false;
    }
    };
