<?php
/**
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
     * API call from View
     * Be carefully, use it for calculate/update/save some data
     * For render information use Widgets!
     *
     * <pre>
     * <code>
     * $this->api($module, $method, array $params);
     * </code>
     * </pre>
     *
     * @param string $module
     * @param string $method
     * @param array $params
     * @return View
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
    }
    };
